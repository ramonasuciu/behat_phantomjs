Feature: Blogpost "Byetta Fails"
    As a website user
    I want to be able to navigate to the "Byetta Fails" blogpost
    And verify all links are functional and all elements are visible

    Scenarios for "Byetta Fails" blogpost

    Scenario: Assert the title of the blogpost
        Given I am on "/diabetes/c/17/2631/byetta-fails"
        Then I should see an ".Page-info-title" element
        And I should see "When Byetta Fails"

    Scenario: Assert the title of the category of the blogpost
        Given I am on "/diabetes/c/17/2631/byetta-fails"
        Then I should see an ".Page-category-titleLink" element
        And I should see "Diabetes"

    Scenario: Category link is functional
        Given I am on "/diabetes/c/17/2631/byetta-fails"
        When I follow "Diabetes"
        Then the url should match "/diabetes/"
        And I should see "What is Diabetes?"

    Scenario Outline: Phases links are functional and open the correct pages
        Given I am on "/diabetes/c/17/2631/byetta-fails"
        When I follow <phases>
        Then the url should match <page_displayed>
        And I should see <text_on_page>

        Examples:
            |phases              |page_displayed                          |text_on_page                           |
            |"Related Conditions"|"/diabetes/c/general/related-conditions"|"Diabetes often leads to"              |

    Scenario Outline: Also See links are functional and open the correct page
        Given I am on "/diabetes/c/17/2631/byetta-fails"
        And I should see "Also See"
        When I follow <also_see_link>
        Then the url should match <page_displayed>
        And I should see <text_on_page>

        Examples:
            |also_see_link     |page_displayed   |text_on_page                            |
            |"Obesity"         |"/obesity/"      |"Keep your child, and yourself, fit"    |
            |"Depression"      |"/depression/"   |"Depression"                            |
            |"Heart Disease"   |"/heart-disease/"|"Heart Disease"                         |
            |"High Cholesterol"|"/cholesterol/"  |"Brush up on your cholesterol knowledge"|

   
    Scenario: Verify "Search" field
        Given I am on "/diabetes/c/17/2631/byetta-fails"
        When I fill in field "search" with "test" and I click on ".Search-button"
        Then I should see "Search Results"


    Scenario: Verify the "View comments" button
        Given I am on "/diabetes/c/17/2631/byetta-fails"
        When I click on the text "View comments"
        Then I should see "Add a comment"

    Scenario: Add a comment
        Given I am on "/diabetes/c/17/2631/comments"
        When I click on the text "Add a comment"
        Then the url should match "/comment"
