<?php declare(strict_types=1);

/**
 * This trait contains common functionality that can be applied to any collection
 */

namespace LDL\Framework\Base\Collection\Traits;

use LDL\Framework\Base\Collection\Contracts\BeforeRemoveInterface;
use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Framework\Base\Collection\Contracts\LockRemoveInterface;
use LDL\Framework\Base\Collection\Contracts\RemoveByKeyInterface;
use LDL\Framework\Base\Contracts\LockableObjectInterface;
use LDL\Framework\Helper\ArrayHelper\ArrayHelper;
use LDL\Framework\Helper\ClassRequirementHelperTrait;
use LDL\Framework\Helper\ComparisonOperatorHelper;
use LDL\Framework\Helper\IterableHelper;

/**
 * Trait RemoveByKeyInterfaceTrait
 * @package LDL\Framework\Base\Collection\Traits
 * @see RemoveByKeyInterface
 */
trait RemoveByKeyInterfaceTrait
{
    use ClassRequirementHelperTrait;

    //<editor-fold desc="RemoveByKeyInterface methods">

    /**
     * @TODO Performance when only one key is passed and the operator is OPERATOR_SEQ (===)
     *
     * @param $key
     * @param string $operator
     * @param string $order
     * @return int
     * @throws \LDL\Framework\Base\Exception\LockingException
     * @throws \LDL\Framework\Helper\ArrayHelper\Exception\InvalidKeyException
     */
    public function removeByKey(
        $key,
        string $operator = ComparisonOperatorHelper::OPERATOR_SEQ,
        string $order = ComparisonOperatorHelper::COMPARE_LTR
    ) : int
    {
        $this->requireImplements([
            CollectionInterface::class,
            RemoveByKeyInterface::class
        ]);

        $this->requireTraits([CollectionInterfaceTrait::class]);

        if($this instanceof LockableObjectInterface){
            $this->checkLock();
        }

        if($this instanceof LockRemoveInterface){
            $this->checkLockRemove();
        }

        ArrayHelper::validateKey($key);

        $removed = 0;

        if(!ComparisonOperatorHelper::isStrictlyEqualsOperator($operator))
        {
            $this->setItems(
                IterableHelper::filter($this, function($val, $k) use ($key, $operator, $order) : bool {
                    $compare = ComparisonOperatorHelper::compare($k, $key, $operator, $order);

                    if(!$compare) {
                        return true;
                    }

                    if($this instanceof BeforeRemoveInterface){
                        $this->getBeforeRemove()->call($this, $this->get($key), $key);
                    }

                    return false;

                }, $removed)
            );

            return $removed;
        }

        if(!$this->hasKey($key)){
            return $removed;
        }

        $this->removeItem($key);

        return ++$removed;
    }

    public function removeLast() : CollectionInterface
    {
        $this->removeByKey($this->getLastKey(),
            ComparisonOperatorHelper::OPERATOR_SEQ,
            ComparisonOperatorHelper::COMPARE_LTR
        );

        return $this;
    }
    //</editor-fold>

}