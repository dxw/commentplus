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

  Scenario: Downloading a data dump as CSV
    Given I am logged in as "admin"
    And option "commentplus" is set to the prescribed dosage
    And there is a page called "TestPost1"
    And the page "TestPost1" has meta "_commentplus" as "one-two-three"
    And I am logged in as "admin"

    Given I am on page "TestPost1"
    Then I fill in "comment_0" with "Southwark"
    And I press "submit_0"

    Given I am on page "TestPost1"
    Then I check "cp0_notforpublication"
    And I choose "cp0_Are_cats_cute__yes"
    And I select "Seabrooks" from "cp0_What_are_your_favourite_crisps_"
    And I fill in "comment_0" with "Borough"
    And I press "submit_0"

    Given I am not logged in

    Given I am on page "TestPost1"
    Then I fill in "author_0" with "Tom Tester"
    And I fill in "email_0" with "tom@tester.testing"
    And I fill in "comment_0" with "Islington"
    And I press "submit_0"

    Given I am on page "TestPost1"
    Then I fill in "author_1" with "Tom Tester"
    And I fill in "email_1" with "tom@tester.testing"
    And I check "cp1_notforpublication"
    And I select "Y'e's" from "Did Tom's quoting test work?"
    And I fill in "comment_1" with "Elephant & Castle"
    And I press "submit_1"

    Given I am on page "TestPost1"
    Then I fill in "author_0" with "Derek Developer"
    And I fill in "email_0" with "telly@holizz.com"
    And I check "cp0_notforpublication"
    And I choose "cp0_Are_cats_cute__yes"
    And I select "Seabrooks" from "cp0_What_are_your_favourite_crisps_"
    And I fill in "comment_0" with "This is some text."
    And I press "submit_0"
