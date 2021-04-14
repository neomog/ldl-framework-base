<?php declare(strict_types=1);

/**
 * This trait contains common functionality that can be applied to any collection
 */

namespace LDL\Framework\Base\Collection\Traits;

use LDL\Framework\Base\Collection\Contracts\AppendableInterface;
use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Framework\Base\Collection\Exception\UndefinedOffsetException;
use LDL\Framework\Base\Contracts\LockableObjectInterface;
use LDL\Framework\Base\Collection\Exception\CollectionException;
use LDL\Framework\Helper\ArrayHelper\ArrayHelper;
use LDL\Framework\Helper\IterableHelper;

trait CollectionInterfaceTrait
{
    use ResetCollectionTrait;

    /**
     * Maintains the count of elements inside the collection
     * @var int
     */
    private $count = 0;

    /**
     * Holds all items
     * @var array
     */
    private $items = [];

    /**
     * Holds the key of the last appended item
     * @var number|string
     */
    private $last;

    /**
     * Holds the key of the first appended item
     * @var number|string
     */
    private $first;

    //<editor-fold desc="CollectionInterface methods">
    public function getFirst()
    {
        if(null === $this->last) {
            $msg = 'Could not obtain first item since this collection is empty';
            throw new CollectionException($msg);
        }

        return $this->items[$this->first];
    }

    public function getFirstKey()
    {
        return $this->first;
    }

    public function getLast()
    {
        if(null === $this->last) {
            $msg = 'Could not obtain last item since this collection is empty';
            throw new CollectionException($msg);
        }

        return $this->items[$this->last];
    }

    public function getLastKey()
    {
        return $this->last;
    }

    public function isEmpty() : bool
    {
        return 0 === $this->count;
    }

    public function hasKey($key) : bool
    {
        return array_key_exists($this->items, $key);
    }

    public function keys() : array
    {
        return array_keys($this->items);
    }

    public function hasValue($value) : bool
    {
        foreach($this as $val){
            if($val === $value){
                return true;
            }
        }

        return false;
    }

    public function map(callable $func) : CollectionInterface
    {
        /**
         * @var CollectionInterface $collection
         */
        $collection = clone($this);
        $map = IterableHelper::map($collection, $func);
        $collection->setItems([]);

        if($collection instanceof AppendableInterface){
            return $collection->appendMany($map);
        }

        $collection->setItems($map);

        return $collection;
    }

    public function filter(callable $func, int $mode=0) : CollectionInterface
    {
        /**
         * @var CollectionInterface $collection
         */
        $collection = clone($this);
        $collection->setItems(IterableHelper::filter($this, $func, $mode));
        return $collection;
    }

    public function toArray(): array
    {
        if(false === $this instanceof LockableObjectInterface){
            return $this->items;
        }

        $items = [];

        foreach($this as $key => $item){
            $items[$key] = is_object($item) ? clone($item) : $item;
        }

        return $items;
    }
    //</editor-fold>


    //<editor-fold desc="Protected methods which are used to manipulate private properties when using this trait">
    protected function setCount(int $count): CollectionInterface
    {
        $this->count = $count;
        return $this;
    }

    protected function setItems(iterable $items): CollectionInterface
    {
        $this->first = null;
        $this->last = null;

        $this->items = IterableHelper::toArray($items);

        $keys = array_keys($this->items);
        $keyCount = count($keys);
        $this->count = $keyCount;

        if(0 === $keyCount){
            return $this;
        }

        if($keyCount === 1) {
            $this->first = $keys[0];
            $this->last = $this->first;

            return $this;
        }

        $this->first = $keys[0];
        $this->last = $keys[$keyCount-1];

        return $this;
    }

    protected function setFirst($first): CollectionInterface
    {
        if(null !== $first){
            ArrayHelper::validateKey($first);
        }

        $this->first = $first;
        return $this;
    }

    protected function setLast($last): CollectionInterface
    {
        if(null !== $last){
            ArrayHelper::validateKey($last);
        }

        $this->last = $last;
        return $this;
    }
    //</editor-fold>

    //<editor-fold desc="\Countable Methods">
    public function count() : int
    {
        return $this->count;
    }
    //</editor-fold>

    //<editor-fold desc="\Iterator Methods">
    public function rewind() : void
    {
        reset($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function valid() : bool
    {
        $key = key($this->items);
        return ($key !== null && $key !== false);
    }

    public function current()
    {
        return current($this->items);
    }
    //<editor-fold>

    //<editor-fold desc="\ArrayAccess methods">
    public function offsetExists($offset) : bool
    {
        ArrayHelper::validateKey($offset);
        return array_key_exists($offset, $this->items);
    }

    public function offsetGet($offset)
    {
        ArrayHelper::validateKey($offset);

        if(!$this->offsetExists($offset)){
            $msg = "Offset \"$offset\" does not exist";
            throw new UndefinedOffsetException($msg);
        }

        return $this->items[$offset];
    }

    public function offsetSet($offset, $value) : void
    {
        $this->replace($value, $offset);
    }

    public function offsetUnset($offset) : void
    {
        $this->remove($offset);
    }
    //</editor-fold>
}