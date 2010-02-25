Feature: Commenting on a post

  Background:
    Given WordPress is installed
    And option "comments_per_page" is set to "2"
    And option "comments_order" is set to "asc"
    And option "default_comments_page" is set to "oldest"
    And plugin "commentplus" is enabled
    And a post called "TestPost1"
    And the post "TestPost1" has meta "_commentplus" as "["Stream1","Stream2","Stream3"]"
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
    Then I should see "Tickle" within "#commentplus_stream_Stream1"
    And I should not see "Tickle" within "#commentplus_stream_Stream2"
    And I should not see "Tickle" within "#commentplus_stream_Stream3"

  Scenario: Comment pagination
    Given I am logged in as "admin"
    And I am on post "TestPost1"

    And I fill in "comment_1" with "stream1_comment1"
    And I press "submit_1"
    And I fill in "comment_1" with "stream1_comment2"
    And I press "submit_1"
    And I fill in "comment_1" with "stream1_comment3"
    And I press "submit_1"
    And I fill in "comment_2" with "stream2_comment1"
    And I press "submit_2"
    And I fill in "comment_2" with "stream2_comment2"
    And I press "submit_2"
    And I fill in "comment_2" with "stream2_comment3"
    And I press "submit_2"

    Then I approve all comments
    Given I am on post "TestPost1"

    Then I should see "stream1_comment1" within "#commentplus_stream_Stream1"
    And I should see "stream1_comment2" within "#commentplus_stream_Stream1"
    And I should not see "stream1_comment3" within "#commentplus_stream_Stream1"

    And I should see "stream2_comment1" within "#commentplus_stream_Stream2"
    And I should see "stream2_comment2" within "#commentplus_stream_Stream2"
    And I should not see "stream2_comment3" within "#commentplus_stream_Stream2"

    And I should not see "Older Comments"

    When I follow "Newer Comments"

    Then I should not see "stream1_comment1" within "#commentplus_stream_Stream1"
    And I should not see "stream1_comment2" within "#commentplus_stream_Stream1"
    And I should see "stream1_comment3" within "#commentplus_stream_Stream1"

    And I should not see "stream2_comment1" within "#commentplus_stream_Stream2"
    And I should not see "stream2_comment2" within "#commentplus_stream_Stream2"
    And I should see "stream2_comment3" within "#commentplus_stream_Stream2"

    And I should not see "Newer Comments"
    And I should see "Older Comments"

  Scenario: Threaded comments
    Given option "thread_comments" is set to "1"
    And I am logged in as "admin"
    And I am on post "TestPost1"

    Then I fill in "comment_1" with "stream1_comment1"
    And I press "submit_1"
    And I fill in "comment_2" with "stream2_comment1"
    And I press "submit_2"

    When I follow "Reply" within "#commentplus_stream_Stream2"
    And I fill in "comment_2" with "stream2_reply1"
    And I press "submit_2"
    Then I should see "stream2_reply1" within "//*[text()='stream2_comment1']/ancestor::li"
