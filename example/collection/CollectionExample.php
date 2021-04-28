<?php declare(strict_types=1);

require __DIR__.'/../../vendor/autoload.php';

use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Framework\Base\Collection\Contracts\AppendableInterface;
use LDL\Framework\Base\Collection\Contracts\KeyFilterInterface;
use LDL\Framework\Base\Collection\Contracts\RemovableInterface;
use LDL\Framework\Base\Collection\Contracts\ReplaceableInterface;
use LDL\Framework\Base\Collection\Contracts\UnshiftInterface;
use LDL\Framework\Base\Collection\Traits\AppendableInterfaceTrait;
use LDL\Framework\Base\Collection\Traits\CollectionInterfaceTrait;
use LDL\Framework\Base\Collection\Traits\KeyFilterInterfaceTrait;
use LDL\Framework\Base\Collection\Traits\RemovableInterfaceTrait;
use LDL\Framework\Base\Collection\Traits\ReplaceableInterfaceTrait;
use LDL\Framework\Base\Collection\Traits\UnshiftInterfaceTrait;
use LDL\Framework\Base\Collection\Traits\AppendManyTrait;

interface MyCollectionInterface extends CollectionInterface, AppendableInterface, KeyFilterInterface, RemovableInterface, ReplaceableInterface, UnshiftInterface
{

}

class MyCollection implements MyCollectionInterface
{
    use CollectionInterfaceTrait;
    use AppendableInterfaceTrait;
    use AppendManyTrait;
    use KeyFilterInterfaceTrait;
    use RemovableInterfaceTrait;
    use ReplaceableInterfaceTrait;
    use UnshiftInterfaceTrait;
}

echo "Create collection\n";

$collection = new MyCollection();

echo "Append single item: 'hello'\n";

$collection->append('hello');

echo "Get value at offset 0\n";

var_dump($collection[0]);

echo "Append many items: 'LDL', 'World'\n";

$collection->appendMany(['LDL', 'World']);

echo "Check items in the collection\n";

foreach($collection as $value){
    echo "Item: $value\n";
}

echo "Replace item 'LDL' with 'crazy'\n";

$collection->replace('crazy', 1);

echo "Check items in the collection\n";

foreach($collection as $value){
    echo "Item: $value\n";
}

echo "Replace item with key 'lol' (not exists), it has append to the collection\n";
$collection->replace('something', 'lol');

echo "Check items in the collection\n";

foreach($collection as $value){
    echo "Item: $value\n";
}

echo "Add item: 'first' at the beginning\n";

$collection->unshift('first');

echo "Get first item: {$collection->getFirst()}\n";