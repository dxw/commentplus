Feature: Configuring individual posts

  Background:
    Given WordPress is installed
    And option "commentplus" is set to "{"Set0":[{"name":"Stream0"}],"Set1":[{"name":"Stream1"}]}"
    And plugin "commentplus" is enabled
    And I am not logged in

  @erratic @probably-a-terrorist
  Scenario: Add a stream set via admin interface
    Given I am logged in as "admin"
    And I am on new page
    Then I fill in "post_title" with "Page0"
    And I select "Set0" from "Stream set"
    And I press "Publish"
    Given I am on new page
    Then I fill in "post_title" with "Page1"
    And I select "Set1" from "Stream set"
    And I press "Publish"
    Given I am on new page
    Then I fill in "post_title" with "Page2"
    And I press "Publish"

    Given I am on page "Page0"
    Then I should see "Stream0"
    And I should not see "Stream1"

    Given I am on page "Page1"
    Then I should see "Stream1"
    And I should not see "Stream0"

    Given I am on page "Page2"
    Then I should not see "Stream0"
    And I should not see "Stream1"

  Scenario: Persisting streamset values
    Given I am logged in as "admin"
    And I am on new page
    Then I fill in "post_title" with "Page0"
    And I select "Set0" from "Stream set"
    And I press "Publish"
    Given I am on edit page "Page0"
    Then the "Stream set" field should contain "Set0"
