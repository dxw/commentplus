Given /^I am not logged in$/ do
  visit path_to 'admin dashboard'
  click_link('Log Out')
end

Given /^a post called "([^\"]*)"$/ do |title|
  visit path_to 'new post'
  fill_in 'title', :with => title
  click_button 'Publish'
end

Given /^the post "([^\"]*)" has meta "([^\"]*)" as "(.*)"$/ do |title,key,value|
  visit path_to %Q%edit post "#{title}"%
  fill_in 'metakeyinput', :with => key
  fill_in 'metavalue', :with => value
  click_button 'Update'
end

Given /^the post "([^\"]*)" has comment_status "([^\"]*)"$/ do |title,status|
  visit path_to %Q%edit post "#{title}"%
  uncheck 'comment_status'
  click_button 'Update'
end
