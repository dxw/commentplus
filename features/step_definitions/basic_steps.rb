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
