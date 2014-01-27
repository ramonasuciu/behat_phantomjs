<?php

//namespace Behat\Mink;

// Require 3rd-party libraries here:

//require_once 'PHPUnit/Autoload.php';
//require_once 'PHPUnit/Framework/Assert/Functions.php';

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\Selenium2Driver,
    Behat\MinkExtension\Context\MinkContext;

#$driver = new \Behat\Mink\Driver\ZombieDriver();


/**
 * Features context.
 */
class FeatureContext extends Behat\MinkExtension\Context\MinkContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml.bak)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * Waits for the defined amount of seconds
     *
     * @Then /^I wait "([^"]*)" seconds$/
     */
    public function iWaitSeconds($arg1)
    {
        sleep($arg1);
    }

    /**
     * Follows link defined with CSS/XPATH path
     *
     * @When /^I follow link defined by path "([^"]*)"$/
     */
    public function iFollowLinkDefinedByCssPath($path)
    {
        if (preg_match('/\s/', $path)) {
            $element = $this->getSession()
                ->getPage()
                ->find("css", $path);
        } else {
            $element = $this->getSession()
                ->getPage()
                ->find("xpath", $path);
        }

        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate selector: "%s"', $path));
        } else {
            if ($element->hasAttribute('href')) {
                $this->visit($element->getAttribute('href'));
            }
            //try to find it higher in dom tree
            $parent = $element;
            $level = 0;
            do {
                $parent = $parent->getParent();
                $level ++;
            } while ($level < 3 && is_object($parent) && ! $parent->hasAttribute('href'));
            if (is_object($parent) && $parent->hasAttribute('href')) {
                $this->visit($parent->getAttribute('href'));
            }
        }
    }

    /**
     * Follows link defined with X path (relative path)
     *
     * @When /^I follow link defined by x path "([^"]*)"$/
     */
    public function iFollowLinkDefinedByXPath($xPathSelector)
    {
        $element = $this->getSession()->getPage()->find('xpath', $xPathSelector);

        if ($element->hasAttribute('href')) {
            $this->visit($element->getAttribute('href'));
        }
        //try to find it higher in dom tree
        $parent = $element;
        $level = 0;
        do {
            $parent = $parent->getParent();
            $level ++;
        } while ($level < 3 && is_object($parent) && ! $parent->hasAttribute('href'));
        if (is_object($parent) && $parent->hasAttribute('href')) {
            $this->visit($parent->getAttribute('href'));
        }
    }

    /**
     * Compares current URL to a specific one
     *
     * @Then /^(?:|I )should be on this "(?P<page>[^"]+)"$/
     */
    public function assertCurrentPageAddress($page)
    {
        $currentPage = $this->getSession()->getCurrentUrl();
        echo "\n Current URL is:   ", $currentPage, "\n";
        echo "\n Requested URL is: ", $page, "\n";
        if ($currentPage == $page) {
            echo "\n Current URL: ", $currentPage, " matches \n requested URL: ", $page, "!\n";
        } else {
            throw new Behat\Gherkin\Exception\Exception("The current page does not not match requested page!\n");
        }
    }

    /**
     * Checks that an element defined by path is visible
     *
     * @Then /^element defined by path "([^"]*)" should be visible$/
     */
    public function elementDefinedByPathShouldBeVisible($path)
    {
        if (preg_match('/\s/', $path)) {
            $page = $this->getSession()
                ->getPage();
            $element = $page->find("css", $path);
        } else {
            $page = $this->getSession()
                ->getPage();
            $element = $page->find("xpath", $path);
        }
//        var_dump($element);

        if (! is_object($element)) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate path selector: "%s"', $path));
        } else {
            (boolean) $result = $element->isVisible();
        }
        if ($result == false) {
            throw new \InvalidArgumentException(sprintf(
                'Element defined by path "%s" is not visible',
                $path
            ));

            return $result;
        }
    }

    /**
     * Check if an image defined by path is displayed
     *
     * @Then /^I should see a custom defined "([^"]*)" image$/
     */
    public function iShouldSeeACustomDefinedElement($path)
    {
        if (preg_match('/\s/', $path)) {
            $element = $this->getSession()
                ->getPage()
                ->find("css", $path);
        } else {
            $element = $this->getSession()
                ->getPage()
                ->find("xpath", $path);
        }
        if (! is_object($element)) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate path selector: "%s"', $path));
        } else {
            if ($element->hasAttribute('src')) {
                print_r("Found the following image:", $element->getAttribute('src'));
            } else {
                throw new \InvalidArgumentException(sprintf('Element defined by path "%s" was not found', $path));
            }
        }
    }

    /**
     * Check element visibility property
     *
     * @Then /^element "([^"]*)" should have visibility enabled$/
     */
    public function elementShouldBeVisible($path)
    {
        if (preg_match('/\s/', $path)) {
            $element = $this->getSession()
                ->getPage()
                ->find("css", $path);
        } else {
            $element = $this->getSession()
                ->getPage()
                ->find("xpath", $path);
        }
        $styles = $element->getAttribute("style");
        echo $styles . "\n";
        if ($styles <> "visibility: visible; opacity: 1;" && $styles <> "") {
            throw new \Behat\Gherkin\Exception\Exception('Element is not visible');
        }
    }

    /**
     * @Then /^I print value of field "([^"]*)"$/
     */
    public function iPrintValueOfField($arg1)
    {
        $element = $this->getSession()
            ->getPage()
            ->find("css", $arg1);
        if ($value = $element->getValue() == ""
        ) {
            throw new Behat\Gherkin\Exception\Exception('Element has no set value');
        } else {
            echo $value;
        }
    }

    /**
     * @When /^I fill in field "([^"]*)" with "([^"]*)" and I click on "([^"]*)"$/
     */
    public function iFillInFieldWith($field, $value, $locator)
    {
        $this->getSession()->getPage()->fillField($field, $value);
        $el = $this->getSession()
            ->getPage()
            ->find('css', $locator)
            ->click();
    }

    /**
     * @Given /(?:I )fill in field "(.*)" with "(.*)"/
     */

    /**
    public function fillCssField($css, $value)
    {
        $this->getMink()->getPage()->find('css', $css)->setValue($value);
    }
    */

    /** Click on the element with the provided xpath query
    *
    * @When /^(?:|I )click on element "([^"]*)"$/
    */
    public function iClickOnElement($locator)
    {
        // runs the actual query and returns the element
        if (preg_match('/\s/', $locator)) {
            $element = $this->getSession()
                ->getPage()
                ->find("css", $locator)
                ->click();
        } else {
            $element = $this->getSession()
                ->getPage()
                ->find("xpath", $locator)
                ->click();
        }

        // errors must not pass silently
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate selector: "%s"', $locator));
        }

        // ok, let's hover it
        $element->click();
    }

    /**
     * Perform hover action on an element
     *
     * @Then /^I hover over the element "([^"]*)"$/
     */
    public function iHoverOverTheElement($locator)
    {
        // runs the actual query and returns the element
        if (preg_match('/\s/', $locator)) {
            $element = $this->getSession()
                ->getPage()
                ->find("css", $locator);
        } else {
            $element = $this->getSession()
                ->getPage()
                ->find("xpath", $locator);
        }

        // errors must not pass silently
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate selector: "%s"', $locator));
        }

        // ok, let's hover it
        $element->mouseOver();
    }

    /**
     * Perform reset browser cache and print timestamp
     *
     * @Given /^I reset browser session and print timestamp$/
     */
    public function iResetBrowserSession()
    {
        // here I select current session and do a soft reset, hard reset can be done using restart()
        $this->getSession()->reset();
        $date = new DateTime();
        $miliseconds = $date->format('U');
        $seconds = $miliseconds / 1000;
        $remainder = round($seconds - ($seconds >> 0), 3) * 1000;
        printf( "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n");
        printf( "Date:      " . $date->format('U = Y-m-d') . "\n");
        printf( "Timestamp: " . $date->format('H:i:s.') .$remainder . "\n");
        printf( "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n");
    }

    /**
     * Perform window switch
     *
     * @Then /^I switch to window "([^"]*)"$/
     */
    public function iSwitchToWindow($name)
    {
        $this->getSession()->switchToWindow($name);
    }

    /**
     * Check slide number from label,content, path
     *
     * @Then /^I check slide label "([^"]*)" with content area "([^"]*)" and item path "([^"]*)"$/
     */
    public function iCheckSlideNumber($labelPath,$contentPath,$itemPath)
    {

        if (preg_match('/\s/', $labelPath)) {
            $label = $this->getSession()
                ->getPage()
                ->find("css", $labelPath);
            $content = $this->getSession()
                ->getPage()
                ->find("css", $contentPath);
            $item = $this->getSession()
                ->getPage()
                ->find("css", $itemPath);
        } else {
            $label = $this->getSession()
                ->getPage()
                ->find("xpath", $labelPath);
            $content = $this->getSession()
                ->getPage()
                ->find("xpath", $contentPath);
            $item = $this->getSession()
                ->getPage()
                ->find("xpath", $itemPath);
        }

        echo 'first shiznnit';
        print_r($this->getSession()->getPage()->find("css",".Slideshow-slideLabel-slideId js-Slideshow-slideId"));
//        print_r($this->getSession()->getPage()->find("css",".Slideshow-slideLabel-slideId js-Slideshow-slideId")->getValue());
//        print_r($this->getSession()->getPage()->find("css",".Slideshow-slideLabel-slideId js-Slideshow-slideId")->getHtml());

        echo 'second shiznnit';
        print_r($this->getSession()->getPage()->find("css",".SlideList-item")->getAttribute("width"));

        exit();

        echo "item\r" ;
        $style = $item->getAttribute("style");
        $item->
            print_r('Style'.$style);
        exit  ;
        $item->getAttribute("width");

        echo "label\r" ;
        print_r($label->getHtml());
        print_r($label->getText());
        print_r($label->getValue());
        echo "content\r" ;
        $style = $content->getAttribute("style");
        if ($style <> "transform: translate3d()" && $styles <> "") {
            throw new \Behat\Gherkin\Exception\Exception('Element is not visible');
        }
//        print_r($style->("transform"));

//        $currentSlide =
//        $slideWidth = $element -> getAttribute("width")
//
//        if ($styles <> "transform: translate3d(,0,0);" && $styles <> "") {
//            throw new \Behat\Gherkin\Exception\Exception('The displayed slide page does not match the requested slide : ',$slideNumber);
//        }
    }

    /**
     * Check slide number
     *
     * @Then /^I check slide number "([^"]*)"$/
     */
    public function checkSlidePage($pageNumber)
    {
        $locator = "//div/div[2]/div[3]/div/div/section/div/div/div/div[3]/div/div";
        // runs the actual query and returns the element
        if (preg_match('/\s/', $locator)) {
            $element = $this->getSession()
                ->getPage()
                ->find("css", $locator);
        } else {
            $element = $this->getSession()
                ->getPage()
                ->find("xpath", $locator);
        }
        // errors must not pass silently
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate selector: "%s"', $locator));
        }

//        transform: translate3d(0px, 0px, 0px)
        $pageWidth = ($pageNumber - 1) * 765;
        printf('Page width:'.$pageWidth."\n");
        $styles = $element->getAttribute("style");
        printf($styles."\n");
        printf('Strpos result => '.strpos($styles, "transform: translate3d(-".$pageWidth."px, 0px, 0px)"));
        if (strpos($styles, "transform: translate3d(".($pageWidth != 0 ? "-".$pageWidth : $pageWidth)."px, 0px, 0px)") == false) {
            throw new \Behat\Gherkin\Exception\Exception('Element is not visible');
        }
    }

    /**
    * Verify links rel attribute
    *
    * @Then /^attribute value should be "([^"]*)"$/
    */
    public function iShouldSeeAttribute($path)
    {
        if (preg_match('/\s/', $path)) {
            $element = $this->getSession()
                ->getPage()
                ->find("css", $path)
                ->getAttribute("rel");
        } else {
            $element = $this->getSession()
                ->getPage()
                ->find("xpath", $path)
                ->getAttribute("rel");
        }

    }

    /**
     * Go to previous page in history
     *
     * @Then /^I go to previous page$/
     */
    public function back()
    {}


    /**
     * @hidden
     *
     * @Then /^I click on the button with id "([^"]*)" number "([^"]*)"$/
     */
    public function clickOnTheButtonWithIDNumber($arg1, $arg2)
    {
        $arg = $arg2 - 1; //most users do not start at 0
        $button = $this->getMainContext()->getSession()->getPage()->findAll('css', $arg1);
        if($button) {
            $button[$arg]->click();
        } else {
            throw new Exception('Form Element not found');
        }
    }

    /**
    * Click some text
    *
    * @When /^I click on the text "([^"]*)"$/
    */
    public function iClickOnTheText($text)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath('xpath', '*//*[text()="'. $text .'"]')
        );
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
        }
 
        $element->click();
 
    }

}
