<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

namespace PcmtCoreBundle\FunctionalTests;

use Behat\Behat\Context\Context;

class WebContext extends \SeleniumBaseContext implements Context
{
    /** @var int */
    private $numberOfResults = 0;

    /**
     * @Given There are :num products
     */
    public function thereIsQuantityOfProducts(int $quantity): void
    {
        $em = $this->getEntityManager();
        $repo = $em->getRepository(\Akeneo\Pim\Enrichment\Component\Product\Model\Product::class);
        $products = $repo->findBy([], null, $quantity);
        if (count($products) < $quantity) {
            throw new \Exception('Too few products on the list.');
        }
    }

    /**
     * @When I press the "Create Attribute" button
     */
    public function iPressTheButtonAndWaitForModalToAppear(): void
    {
        $settingsTab = $this->getSession()->getPage()->find('css', '#attribute-create-button');
        $settingsTab->click();
    }

    /**
     * @And I select :attributeType on modal
     * @When I select :attributeType on modal
     */
    public function iChooseAttributeType(string $attributeType): void
    {
        $attributeTypeSpans = $this->getSession()->getPage()->findAll(
            'css',
            'body > div.modal.in > div.AknFullPage > div > div.modal-body > div > span'
        );
        foreach ($attributeTypeSpans as $attributeTypeSpan) {
            if ($attributeTypeSpan->getText() === $attributeType) {
                $attributeTypeSpan->click();
                $this->getSession()->wait('1000');

                return;
            }
        }
        throw new \Exception('Did not find matching attribute type to ' . $attributeType);
    }

    /**
     * @When I choose :group option
     * @And I choose :group option
     */
    public function iChooseOption(string $option): void
    {
        $attributeGroupSelect = $this->getSession()->getPage()->findAll('css', '#select2-drop > ul > li');
        foreach ($attributeGroupSelect as $selectOption) {
            if ($selectOption->getText() === $option) {
                $selectOption->click();
                $this->getSession()->wait('1000');

                return;
            }
        }
        throw new \Exception('Did not find option matching to: ' . $option);
    }

    /**
     * @When I choose :attribute1 :attribute2 :attribute3 attributes for concatenated fields
     * @And I choose :attribute1 :attribute2 :attribute3 for concatenated fields
     */
    public function iChooseAttributesForConcatenatedFields(string $attribute1, string $attribute2, string $attribute3): void
    {
        $attributesSelect['#s2id_pcmt_enrich_form_attribute1 > a'] = $attribute1;
        $attributesSelect['#s2id_pcmt_enrich_form_attribute2 > a'] = $attribute2;
        $attributesSelect['#s2id_pcmt_enrich_form_attribute3 > a'] = $attribute3;

        foreach ($attributesSelect as $selector => $option) {
            $select = $this->getSession()->getPage()->find('css', $selector);
            $select->click();
            $this->iChooseOption($option);
        }
    }

    /**
     * @And I save
     * @When I save
     */
    public function iSave(): void
    {
        $settingsTab = $this->getSession()->getPage()->find(
            'css',
            'div.AknTitleContainer-rightButton > button'
        );
        $settingsTab->click();
    }

    /**
     * @When I check :num products
     */
    public function iCheckProducts(int $num): void
    {
        $this->waitForThePageToLoad();
        $checkboxes = $this->getSession()->getPage()
            ->findAll('css', 'td.AknGrid-bodyCell > input[type=checkbox]');
        for ($i = 0; $i < $num; $i++) {
            $checkbox = $checkboxes[$i];
            $checkbox->click();
        }
    }

    /**
     * @When I click delete
     */
    public function iClickDelete(): void
    {
        $this->getSession()->wait('200');
        $buttons = $this->getSession()->getPage()
            ->findAll('css', 'a.AknButton--important');
        foreach ($buttons as $button) {
            if ('Delete' === $button->getText()) {
                break;
            }
        }
        if (empty($button)) {
            throw new \Exception('No delete button.');
        }
        $button->click();
    }

    /**
     * @When I confirm delete
     */
    public function iConfirmDelete(): void
    {
        $this->getSession()->wait('2000');
        $buttons = $this->getSession()->getPage()
            ->findAll('css', 'div.AknButton--important');
        foreach ($buttons as $button) {
            if ('Delete' === $button->getText()) {
                break;
            }
        }
        if (empty($button)) {
            throw new \Exception('No delete confirmation button.');
        }
        $button->click();
    }

    /**
     * @Then the number of results should be lower by :quantity, try :attempts times
     */
    public function theNumberOfResultsShouldBeLowerBy(int $quantity, int $attempts): void
    {
        // we make a number of attempts, as the job in background may last for some time
        for ($i = 0; $i < $attempts; $i++) {
            $this->clickLink('Activity');
            $this->waitForThePageToLoad();
            $this->clickLink('Products');
            $this->waitForThePageToLoad();

            $previous = $this->numberOfResults;
            $newNumber = $this->getNumberOfResultsFromResultsPage();
            if ($previous - $quantity === $newNumber) {
                break;
            }
        }

        if ($previous - $quantity !== $newNumber) {
            throw new \Exception('Wrong number of results. Should be: ' . round($previous - $quantity));
        }
    }

    public function getNumberOfResultsFromResultsPage(): int
    {
        $resultsText = $this->getSession()
            ->getPage()
            ->find('css', 'div.AknTitleContainer-title > div')
            ->getText();

        $resultsCount = explode(' ', $resultsText);

        return (int) $resultsCount[0];
    }

    /**
     * @Given I read number of products
     */
    public function iReadNumberOfProducts(): void
    {
        $this->numberOfResults = $this->getNumberOfResultsFromResultsPage();
    }

    /**
     * @And I should delete created attribute
     * @Then I should delete created attribute
     */
    public function iShouldDeleteCreatedAttribute(): void
    {
        $page = $this->getSession()->getPage();
        $deleteSelector = $page->find('css', 'a.AknIconButton.AknIconButton--small.AknIconButton--trash.AknButtonList-item');
        $deleteSelector->click();

        // once modal appears
        $deleteButton = $page->find('css', 'div.AknButton.AknButtonList-item.AknButton--apply.AknButton--important.ok');
        $deleteButton->click();
        $this->waitForThePageToLoad();
    }
}