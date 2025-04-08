<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Persistence\Dql\Postgresql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use function class_exists;


class MimeSubType extends FunctionNode
{
    public Node|string|null $field = null;

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

        $this->field = $parser->ArithmeticPrimary();

        $parser->match($tokenType::T_CLOSE_PARENTHESIS);
    }


    /**
     * @throws \Doctrine\ORM\Query\AST\ASTException
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'SUBSTRING(%1$s, POSITION(\'/\' in %1$s)+1, LENGTH(%1$s))',
            $this->field->dispatch($sqlWalker)
        );
    }

}
