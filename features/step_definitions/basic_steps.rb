Given /^the post "([^\"]*)" has comment_status "([^\"]*)"$/ do |title,status|
  Given 'I am logged in as "admin"'
  visit path_to %Q%edit post "#{title}"%
  uncheck 'comment_status'
  click_button 'Update'
  Given 'I am not logged in'
end

Then /^I should see "([^\"]*)" within "([^\"]*)"$/ do |text, selector|
  within(selector) do |content|
    content.dom.inner_text.should include text
  end
end

Then /^I should not see "([^\"]*)" within "([^\"]*)"$/ do |text, selector|
  within(selector) do |content|
    content.dom.inner_text.should_not include text
  end
end

Then /^I approve all comments$/ do
  WordPress.mysql.query(%Q'UPDATE #{WordPress.TABLE_PREFIX}comments SET comment_approved=1')
end

Given /^the "([^\"]*)" theme contains "([^\"]*)" with "([^\"]*)"$/ do |theme, file, contents|
  f = File.join('../../themes',theme,file)
  $files << f
  open(f,'w+') do |file|
    file.write(contents)
  end
end

Given /^option "([^\"]*)" is set to the prescribed dosage$/ do |arg1|
  Given %q&option "commentplus" is set to "{"one-two-three":[{"name":"Stream1","fields":[{"name":"Are cats cute?","type":"yesno"},{"name":"What are your favourite crisps?","type":"select","options":[{"slug":"seabrooks","title":"Seabrooks"},{"slug":"walkers","title":"Walkers"}]}]},{"name":"Stream2","fields":[{"name":"Did Tom's quoting test work?","type":"select","options":[{"slug":"y'e's","title":"Y'e's"},{"slug":"n'o","title":"N'o"}]}]},{"name":"Stream3"}]}"&
end

Then /^"([^\"]*)" should not be a link$/ do |text|
  lambda { click_link(text) }.should raise_exception Webrat::NotFoundError
end

Then /^"([^\"]*)" should link to "([^\"]*)"$/ do |text, url|
  u = current_url
  click_link text
  current_url.should == url
  visit u
end

Given /^there is data$/ do
  Given 'I am logged in as "admin"'
  And 'option "commentplus" is set to the prescribed dosage'
  And 'there is a page called "TestPost1"'
  And 'the page "TestPost1" has meta "_commentplus" as "one-two-three"'

  And 'I am logged in as "admin"'

  Given 'I am on page "TestPost1"'
  Then 'I fill in "comment_0" with "Southwark"'
  And 'I press "submit_0"'

  Given 'I am on page "TestPost1"'
  Then 'I check "cp0_notforpublication"'
  And 'I choose "cp0_Are_cats_cute__yes"'
  And 'I select "Seabrooks" from "cp0_What_are_your_favourite_crisps_"'
  And 'I fill in "comment_0" with "Borough"'
  And 'I press "submit_0"'

  Given 'I am not logged in'

  Given 'I am on page "TestPost1"'
  Then 'I fill in "author_0" with "Tom Tester"'
  And 'I fill in "email_0" with "tom@tester.testing"'
  And 'I fill in "comment_0" with "Islington"'
  And 'I press "submit_0"'

  Given 'I am on page "TestPost1"'
  Then 'I fill in "author_1" with "Tom Tester"'
  And 'I fill in "email_1" with "tom@tester.testing"'
  And 'I check "cp1_notforpublication"'
  And 'I select "Y\'e\'s" from "Did Tom\'s quoting test work?"'
  And 'I fill in "comment_1" with "Elephant & Castle"'
  And 'I press "submit_1"'

  Given 'I am on page "TestPost1"'
  Then 'I fill in "author_0" with "Derek Developer"'
  And 'I fill in "email_0" with "telly@holizz.com"'
  And 'I check "cp0_notforpublication"'
  And 'I choose "cp0_Are_cats_cute__yes"'
  And 'I select "Seabrooks" from "cp0_What_are_your_favourite_crisps_"'
  And 'I fill in "comment_0" with "This is some text."'
  And 'I press "submit_0"'
end

Then /^I should be at Comment\+ settings$/ do
  uri = URI.parse(current_url)
  "#{uri.path}?#{uri.query}".should include('options-general.php?page=commentplus/admin_interface.php')
end

Then /^I should receieve a zip file$/ do
  response.header['content-type'].should == 'application/zip'
end

Given /^there is a a config file$/ do
  file = '../../themes/default/commentplus.json'
  open(file, 'w+') do |f|
    f.write '{"one-two-three":[{"name":"Stream1","fields":[{"name":"How many lightbulb jokes does it take to screw in a lightbulb?","type":"yesno"}]}]}'
  end
  $files << file
end
