Rewards Tally block
===================

The Rewards Tally plugin provides a block to display the achievement points, or 
reward points, accumulated by the user and the user communities associated with 
the school or institution. 'User communities' could mean year groups, 'houses' or 
any other logical grouping used at the school.

Background
----------

Schools typically operate some sort of rewards procedure where students accumulate 
reward points for various aspects of their school life, such as good behaviour, 
good work or other positive behaviours. Often, these rewards points are stored in
school Management Information Systems (MIS) or on some other external IT system 
rather than stored directly in Moodle. In this scenario, it can be difficult to 
display either a student's or the whole school's rewards data in Moodle. One option 
might be to use a simple HTML block to point to an HTML page rendered by an external server
that shows a running tally of rewards points, but this is crude and it would be 
difficult to show an individual user's rewards point total since it would not be easy
to associate their user ID or name with the page request.

How this block helps
---------------------

This block provides a consistent framework for presenting user reward tallies. Assuming
this data is stored on an external IT system, a sample PHP script is provided (rewardsrpc.php) that can be
configured to match a local institution's use case with PHP functions for the user and community
points totals.

The Moodle block initiates a remote procedure call on the external system, and receives the data
back via JSON. This is then processed internally and presented to the user front-end block. It 
is fully customisable via the admin settings (site administration) pages. Examples of customisations include:

* whether or not to show individual user points tally
* the names of 'communities' and any associated colour codes
* whether to sort communities' point scores to create a 'leader board'

Maintainer
----------

This block has been written and is currently maintained by P. Reid