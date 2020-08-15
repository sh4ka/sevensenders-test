# Seven Senders Test

The main idea that I wanted to put to work was an api that was fast. For
that I am using a cache layer (filesystem, should be moved to memory in 
the real world)

Given that I receive 2 different files I decided that my solution should
be flexible to allow the users to decide if they want to have those two
files combined or to operate them separately.

# File processor

The idea behind this console command is to facilitate the processing of
xml and json files separately in the input directory.

# Api

The api uses the generated output file or the 2 separated input files
if the command was never executed. This change is transparent to the
user and both responses are equal.