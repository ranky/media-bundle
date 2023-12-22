<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\Persistence\Orm\Filter;


use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\Driver;
use Ranky\SharedBundle\Filter\Visitor\Extension\FilterExtensionVisitor;
use Ranky\SharedBundle\Infrastructure\Persistence\Orm\UidMapperPlatform;

class DoctrineUlidFilterExtensionVisitor implements FilterExtensionVisitor
{

    public function __construct(private readonly UidMapperPlatform $uidMapperPlatform)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function visit(ConditionFilter $filter, Criteria $criteria): ConditionFilter
    {
        $value = $filter->value();

        if (is_array($value) && $value[0] instanceof MediaId) {
            $expression = \sprintf('%s IN (%s)', $filter->field(), ':ulids');
            $filter->expression()->setExpression($expression);
            foreach ($value as $key => $mediaId) {
                $value[$key] = $this->uidMapperPlatform->convertToDatabaseValue($mediaId);
            }
            $filter->expression()->setParameters([
                ':ulids' => $value,
            ]);
            return $filter;
        }
        
        if (!$value instanceof MediaId) {
            return $filter;
        }

        $expression = \sprintf('%s = %s', $filter->field(), ':ulid');
        $filter->expression()->setExpression($expression);
        $filter->expression()->setParameters([
            ':ulid' => $this->uidMapperPlatform->convertToDatabaseValue($value),
        ]);


        return $filter;
    }

    public function support(ConditionFilter $filter, Criteria $criteria): bool
    {
        return $filter->field() === 'm.id'
            && \is_a($criteria::modelClass(), Media::class, true);
    }

    public static function driver(): string
    {
        return Driver::DOCTRINE_ORM->value;
    }
}
