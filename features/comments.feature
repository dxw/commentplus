Feature: Commenting on a post

  Background:
    Given WordPress is installed
    And plugin "commentplus" is enabled

  Scenario: Comment+ appears on posts
    Given a post called "TestPost1"
    And the post "TestPost1" has meta "commentplus" as "["Stream1","Stream2","Stream3"]"

    Given I am on post "TestPost1"
    Then I should see "Stream1"
    And I should see "Stream2"
    And I should see "Stream3"

  Scenario: Comment+ doesn't appear when no-comments is set
    Given a post called "TestPost2"
    And the post "TestPost2" has meta "commentplus" as "["Stream1","Stream2","Stream3"]"
    And the post "TestPost2" has comment_status "closed"

    Given I am on post "TestPost2"
    Then I should see "Comments are closed"
    And I should not see "Stream1"
    And I should not see "Stream2"
    And I should not see "Stream3"
