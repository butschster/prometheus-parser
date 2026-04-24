<?php

declare(strict_types=1);

namespace Butschster\Prometheus;

use Butschster\Prometheus\Ast\SchemaNode;
use Butschster\Prometheus\Exceptions\GrammarFileNotFoundException;
use Butschster\Prometheus\Exceptions\ParseException;
use Butschster\Prometheus\Exceptions\UnexpectedTokenException;
use Butschster\Prometheus\Validation\ValidatorInterface;
use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Lexer\Lexer;
use Phplrt\Parser\Parser as PhplrtParser;
use Phplrt\Parser\BuilderInterface;
use Phplrt\Parser\ContextInterface;
use Phplrt\Contracts\Parser\ParserInterface;

final class Parser
{
    private readonly ParserInterface $parser;

    /** @var ValidatorInterface[] */
    private array $validators = [];

    public function __construct(array $grammar)
    {
        $lexer = $this->createLexer($grammar);
        $builder = $this->createBuilder($grammar['reducers']);

        $this->parser = $this->createParser($lexer, $grammar, $builder);
    }

    /**
     * Register a validator to be applied after each successful parse.
     */
    public function addValidator(ValidatorInterface $validator): static
    {
        $this->validators[] = $validator;
        return $this;
    }

    /**
     * Parse schema
     * @throws ParseException
     * @throws \Butschster\Prometheus\Exceptions\ValidationException
     * @psalm-suppress PossiblyUndefinedMethod
     */
    public function parse(string $schema): ?SchemaNode
    {
        try {
            /**
             * @psalm-suppress InvalidArgument
             */
            $parsed = (array)$this->parser->parse($schema);
            $result = $parsed[0] ?? null;
        } catch (\Phplrt\Contracts\Exception\RuntimeExceptionInterface $e) {
            $this->rethrowAsParseException($e);
        }

        if ($result !== null) {
            foreach ($this->validators as $validator) {
                $validator->validate($result);
            }
        }

        return $result;
    }

    /**
     * Create Lexer from compiled data.
     */
    private function createLexer(array $data): LexerInterface
    {
        \assert(\is_array($data['tokens']), 'Invalid tokens');
        \assert(\is_array($data['tokens']['default']), 'Invalid default tokens');
        \assert(\is_array($data['skip']), 'Invalid skip tokens');

        return new Lexer(
            $data['tokens']['default'],
            $data['skip']
        );
    }

    /**
     * Create AST builder from compiled data.
     * @param array<non-empty-string, callable> $reducers
     */
    private function createBuilder(array $reducers): BuilderInterface
    {
        return new class($reducers) implements BuilderInterface {
            public function __construct(
                /** @var array<non-empty-string, callable>*/
                private readonly array $reducers
            ) {
            }

            public function build(ContextInterface $context, $result)
            {
                $state = $context->getState();

                return isset($this->reducers[$state])
                    ? $this->reducers[$state]($context, $result)
                    : $result;
            }
        };
    }

    /**
     * Create Parser from compiled data.
     */
    private function createParser(LexerInterface $lexer, array $data, BuilderInterface $builder): ParserInterface
    {
        \assert(\is_iterable($data['grammar']), 'Grammar is not defined');
        \assert(isset($data['initial']), 'Grammar does not contain initial state.');

        return new PhplrtParser($lexer, $data['grammar'], [
            // Recognition will start from the specified rule.
            PhplrtParser::CONFIG_INITIAL_RULE => $data['initial'],

            // Rules for the abstract syntax tree builder.
            // In this case, we use the data found in the compiled grammar.
            PhplrtParser::CONFIG_AST_BUILDER => $builder,
        ]);
    }

    /**
     * Rethrow a phplrt runtime exception as a typed ParseException.
     *
     * @throws ParseException
     * @return never
     */
    private function rethrowAsParseException(\Phplrt\Contracts\Exception\RuntimeExceptionInterface $e): void
    {
        $token = $e->getToken();

        $message = \sprintf(
            'Unexpected token "%s" at offset %d: %s',
            $token->getValue(),
            $token->getOffset(),
            $e->getMessage()
        );

        throw new UnexpectedTokenException($message, (int)$e->getCode(), $e);
    }

    private function ensureGrammarFileExists(string $grammarFilePatch): void
    {
        if (!file_exists($grammarFilePatch)) {
            throw new GrammarFileNotFoundException(
                "File {$grammarFilePatch} not found"
            );
        }
    }
}

