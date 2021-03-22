<?php declare(strict_types=1);

namespace LDL\Framework\Base\Collection\Contracts;

use LDL\Framework\Base\Exception\LockingException;

interface LockRemoveInterface
{
    /**
     * Instead of doing if($this->isLockAppend()) { throw new LockingException(...); } in every call
     * just use this method for simplicity.
     *
     * @throws LockingException
     */
    public function checkLockRemove() : void;

    /**
     * @return CollectionInterface
     */
    public function lockRemove(): CollectionInterface;

    /**
     * @return bool
     */
    public function isRemoveLock() : bool;
}