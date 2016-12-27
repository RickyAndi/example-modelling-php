<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use app\models\Skp;
use app\models\SkpItem;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private $skp;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->skp = new Skp();
    }

    /**
    * @When I add the skpItem to the skp with nama :nama
    */
    public function addSkpItemToSkp($nama)
    {
        $this->skp->addSkpItems(new SkpItem($nama));
    }

    /**
    * @Then the number of skpitem should be :skpItemCount
    */
    public function theSkpItemCountShouldBe($skpItemCount)
    {
        PHPUnit_Framework_Assert::assertSame(
            count($this->skp->getSkpItems()),
            intval($skpItemCount)
        );
    }

}
