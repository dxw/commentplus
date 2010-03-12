Feature: Commenting on a post

  Background:
    Given WordPress is installed
    And option "comments_per_page" is set to "2"
    And option "comments_order" is set to "asc"
    And option "default_comments_page" is set to "oldest"
    And option "commentplus" is set to the prescribed dosage
    And plugin "commentplus" is enabled
    And there is a post called "TestPost1"
    And the post "TestPost1" has meta "_commentplus" as "one-two-three"
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
    When I fill in "author_0" with "Tom Tester"
    And I fill in "email_0" with "tom@thedextrousweb.com"
    And I fill in "comment_0" with "Tickle the tester to test the test."
    And I press "submit_0"
    Then I should see "Tickle" within "#commentplus_stream_Stream1"
    And I should not see "Tickle" within "#commentplus_stream_Stream2"
    And I should not see "Tickle" within "#commentplus_stream_Stream3"

  Scenario: Comment pagination
    Given I am logged in as "admin"
    And I am on post "TestPost1"

    And I fill in "comment_0" with "stream1_comment1"
    And I press "submit_0"
    And I fill in "comment_0" with "stream1_comment2"
    And I press "submit_0"
    And I fill in "comment_0" with "stream1_comment3"
    And I press "submit_0"
    And I fill in "comment_1" with "stream2_comment1"
    And I press "submit_1"
    And I fill in "comment_1" with "stream2_comment2"
    And I press "submit_1"
    And I fill in "comment_1" with "stream2_comment3"
    And I press "submit_1"

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

    Then I fill in "comment_0" with "stream1_comment1"
    And I press "submit_0"
    And I fill in "comment_1" with "stream2_comment1"
    And I press "submit_1"

    When I follow "Reply" within "#commentplus_stream_Stream2"
    And I fill in "comment_1" with "stream2_reply1"
    And I press "submit_1"
    Then I should see "stream2_reply1" within "//*[text()='stream2_comment1']/ancestor::li"

  Scenario: Commenting on one stream after hitting reply on another
    Given option "thread_comments" is set to "1"
    And I am logged in as "admin"
    And I am on post "TestPost1"

    Then I fill in "comment_0" with "stream1_comment1"
    And I press "submit_0"
    And I fill in "comment_1" with "stream2_comment1"
    And I press "submit_1"

    When I follow "Reply" within "#commentplus_stream_Stream2"
    And I fill in "comment_0" with "stream1_reply1"
    And I press "submit_0"
    Then I should see "stream1_reply1" within "//*[@id='commentplus_stream_Stream1']"
    And I should not see "stream1_reply1" within "//*[text()='stream2_comment1']/ancestor::li"

  Scenario: Importing comments from theme
    Given I am on post "TestPost1"
    Then I should see "Stream1"
    And I should not see "Testing1"

    Given the "default" theme contains "commentplus_comments.php" with "Testing1"
    And I am on post "TestPost1"
    Then I should see "Testing1"
    And I should not see "Stream1"

  Scenario: Importing respond from theme
    Given I am on post "TestPost1"
    Then I should see "Website"
    And I should not see "Testing2"

    Given the "default" theme contains "commentplus_respond.php" with "Testing2"
    And I am on post "TestPost1"
    Then I should see "Testing2"
    And I should not see "Website"

  Scenario: Extra questions
    Given I am on post "TestPost1"
    Then I should see "Are cats cute?" within "#commentform_0"
    And I should see "What are your favourite crisps?" within "#commentform_0"
    And I should see "Did Tom's quoting test work?" within "#commentform_1"

    When I fill in "author_0" with "Tom Tester"
    And I fill in "email_0" with "tom@example.com"
    And I fill in "comment_0" with "This is a bizzare consultation."
    And I press "submit_0"

    Then I approve all comments
    Given I am on post "TestPost1"

    Then I should see "This is a bizzare consultation." within ".commentlist"
    And I should see "Are cats cute?" within "//dl[@class='commentplus_extra']/dt[1]"
    And I should see "No response" within "//dl[@class='commentplus_extra']/dd[1]"
    And I should see "What are your favourite crisps?" within "//dl[@class='commentplus_extra']/dt[2]"
    And I should see "No response" within "//dl[@class='commentplus_extra']/dd[2]"

  Scenario: More extra questions
    Given I am on post "TestPost1"
    Then I should see "Are cats cute?" within "#commentform_0"
    And I should see "What are your favourite crisps?" within "#commentform_0"
    And I should see "Did Tom's quoting test work?" within "#commentform_1"

    When I fill in "author_0" with "Tom Tester"
    And I fill in "email_0" with "tom@example.com"
    And I choose "Yes"
    And I select "Seabrooks" from "What are your favourite crisps?"
    And I fill in "comment_0" with "This is a bizzare consultation."
    And I press "submit_0"

    Then I approve all comments
    Given I am on post "TestPost1"

    Then I should see "Are cats cute?" within "//dl[@class='commentplus_extra']/dt[1]"
    And I should see "Yes" within "//dl[@class='commentplus_extra']/dd[1]"
    And I should see "What are your favourite crisps?" within "//dl[@class='commentplus_extra']/dt[2]"
    And I should see "Seabrooks" within "//dl[@class='commentplus_extra']/dd[2]"

  Scenario: Quotes
    Given I am logged in as "admin"
    And I am on post "TestPost1"
    And I fill in "comment_1" with "You there! Yes, you. Stop looking at naughty things on the Internet!"
    And I select "Y'e's" from "Did Tom's quoting test work?"
    And I press "submit_1"

    Then I approve all comments
    Given I am on post "TestPost1"

    Then I should see "naughty things on the Internet" within "#commentplus_stream_Stream2 .commentlist"
    And I should see "Did Tom's quoting test work?" within "//dl[@class='commentplus_extra']/dt[1]"
    And I should see "Y'e's" within "//dl[@class='commentplus_extra']/dd[1]"

  Scenario: Not for publication
    Given I am on post "TestPost1"
    And I fill in "author_0" with "Tom Tester"
    And I fill in "email_0" with "tom@example.org"
    And I fill in "comment_0" with "A senior politician does something Daily Mail readers would find abhorent"
    And I select "Seabrooks" from "What are your favourite crisps?"
    And I check "Not for publication"
    And I press "submit_0"

    Then I approve all comments
    Given I am on post "TestPost1"

    Then I should not see "Tom Tester"
    And I should not see "Daily Mail readers"
    And I should not see "What are your favourite crisps?" within ".commentlist"
    And I should not see "Seabrooks" within ".commentlist"
    And I should see "This reply is marked not for publication."

    Given I am logged in as "admin"
    And I am on post "TestPost1"

    Then I should see "Tom Tester"
    And I should see "Daily Mail readers"
    And I should see "What are your favourite crisps?" within ".commentlist"
    And I should see "Seabrooks" within ".commentlist"
    And I should see "This reply is marked not for publication."
