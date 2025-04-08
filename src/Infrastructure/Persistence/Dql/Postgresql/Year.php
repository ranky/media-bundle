<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Persistence\Dql\Postgresql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\TokenType;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use function class_exists;


class Year extends FunctionNode
{
    public Node|string|null $date = null;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return \sprintf(
            'EXTRACT(YEAR FROM %s)',
            $sqlWalker->walkArithmeticPrimary($this->date)
        );
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function parse(Parser $parser): void
    {
        if (!class_exists('Doctrine\ORM\Query\TokenType')) {
            $tokenType = 'Doctrine\ORM\Query\Lexer';
        } else {
            $tokenType = 'Doctrine\ORM\Query\TokenType';
        }
        $parser->match($tokenType::T_IDENTIFIER);
        $parser->match($tokenType::T_OPEN_PARENTHESIS);

        $this->date = $parser->ArithmeticPrimary();

        $parser->match($tokenType::T_CLOSE_PARENTHESIS);
    }
}
