Feature: Links on Behat page are working properly

    Scenario: Assert correct page is displayed when clicking on a link from the behat.org page
        Given I am on "/"
        When I click on the text "GitHub"
        Then the URL should match "/Behat"