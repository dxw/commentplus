Given /^a post called "([^\"]*)"$/ do |title|
  visit path_to 'new post'
  fill_in 'title', :with => title
  click_button 'Publish'
end

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
