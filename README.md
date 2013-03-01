jstor-early-journal-content
===========================

Tools to extract data from JSTOR Early Journal Content Data Bundle. Currently extracts basic bibliographic metadata and creates RIS format files for each journal (identified by ISSN).

The [JSTOR Early Journal Content Data Bundle](http://dfr.jstor.org/??view=text&&helpview=about_ejc) comes as 7.4 Gb tarball that when extracted creates a directory that is too large for Unix tools like ls to list.

To generate a list of files use -t flag:

 tar -tvf bundle.tar > files.txt

The file files.txt now contains a list of all files in the bundle folder, and can then be parsed to access each file individually.