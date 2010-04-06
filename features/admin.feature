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
    Given there is data
    And I am logged in as "admin"
    And I am on admin dashboard
    When I follow "Comment+" within "#menu-settings"
    Then I should be at Comment+ settings

    When I follow "Download CSV"
    Then I should receieve a zip file

  Scenario: Not for publication in admin interface
    Given I am logged in as "admin"
    And option "commentplus" is set to the prescribed dosage
    And there is a page called "TestPost1"
    And the page "TestPost1" has meta "_commentplus" as "one-two-three"

    Given I am not logged in
    And I am on post "TestPost1"
    And I fill in "author_0" with "Tom Tester"
    And I fill in "email_0" with "tom@example.org"
    And I fill in "comment_0" with "A senior politician does something Daily Mail readers would find abhorent"
    And I select "Seabrooks" from "What are your favourite crisps?"
    And I check "Not for publication"
    And I press "submit_0"

    Then I approve all comments

    Given I am logged in as "admin"
    And I am on edit comments
    Then I should see "Tom Tester"
    And I should see "tom@example.org"
    And I should see "Daily Mail readers"
    And I should see "What are your favourite crisps?"
    And I should see "Seabrooks"
    And I should see "This reply is marked not for publication."
