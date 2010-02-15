Feature: Commenting on a post

  Background:
    Given WordPress is installed
    And plugin "commentplus" is enabled
    And a post called "TestPost1"
    And the post "TestPost1" has meta "commentplus" as "["Stream1","Stream2","Stream3"]"
    And I am not logged in

  Scenario: Comment+ appears on posts
    Given I am on post "TestPost1"
    Then I should see "Stream1"
    And I should see "Stream2"
    And I should see "Stream3"

  Scenario: Comment+ doesn't appear when no-comments is set
    Given the post "TestPost1" has comment_status "closed"
    And I am on post "TestPost1"
    Then I should see "Comments are closed"
    And I should not see "Stream1"
    And I should not see "Stream2"
    And I should not see "Stream3"

  Scenario: Commenting on a stream
    Given I am on post "TestPost1"
    When I fill in "author_1" with "Tom Tester"
    And I fill in "email_1" with "tom@thedextrousweb.com"
    And I fill in "comment_1" with "Tickle the tester to test the test."
    And I press "Submit Comment"
    Then I should see "Tickle" within "#commentplus_stream_1"
    And I should not see "Tickle" within "#commentplus_stream_2"
    And I should not see "Tickle" within "#commentplus_stream_3"
