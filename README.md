# Summer 23 Research Documentation

## Introduction

This documentation is split up into three sections: Tools, Website, and BCG List. "Tools" details the coding tools I have created and provided and how to use them. "Website" details the structure of the ACCEPT2 website and how to make necessary changes or additions. "BCG List" details the final process of cleaning up the BCG list.

## Tools

As of now, there are three main tools: 
"figureset_maker.ipynb", a jupyter notebook that is used to create an AAS figureset for each cluster; 
"cluster_pointers.ipynb", a jupyter notebook that creates an html table with the name of each cluster, its coordinates and redshift, and whether or not it has global properties, profiles, morphology, and in ACCEPT1, this is used as a sort of index page for the ACCEPT2 website; 
"table_generate.ipynb", a jupyter notebook that takes a csv and returns both an HTML and MRT table. usage documentation is contained within the notebook

The file structure within the tools folder and explanation of the tools is as follows:

```
tools
 ┣ data
 ┃ ┣ globalTestFormat.csv: a csv of the ACCEPT2 global properties table, used for input
 ┃ ┣ ACCEPT2_K0_and_z.csv: a csv of profile data for the clusters
 ┃ ┗ ACCEPT1.txt: the ACCEPT1 table in ascii format
 ┣ figureset_maker
 ┃ ┣ figureset.txt: the outputed figureset AASTeX
 ┃ ┗ figureset_maker.ipynb: a jupyter notebook that is used to create an AAS figureset for each cluster. note: to work, the figuresets folder would also need to be within this folder, however to save space on this repository it has been excluded
 ┣ cluster_pointers
 ┃ ┣ cluster_pointers.ipynb: a jupyter notebook that creates an html table with the name of each cluster, its coordinates and redshift, and whether or not it has global properties, profiles, morphology, and in ACCEPT1
 ┃ ┗ cluster_pointers.html: the outputed html table created from cluster_pointers.ipynb
 ┗ table_generate
   ┣ table_generate_output
   ┃ ┗ globalTestFormat: a folder automatically generated by table_generate.ipynb, named based on the csv it was generated from
   ┃   ┣ globalTestFormat.dat: an MRT table generated from the csv by table_generate.ipynb
   ┃   ┣ globalTestFormat.html: an html table generated from the csv by table_generate.ipynb
   ┃   ┣ search.php: a php script to generate a VOTable unique to the table
   ┃   ┗ globalTestFormat.csv: a csv of the ACCEPT2 global properties table, needed for conesearch.php to work
   ┣ table_generate.ipynb: a jupyter notebook that takes a csv and returns both an HTML and MRT table. usage documentation contained within notebook
   ┗ search.php: a template script to generate a VOTable based on a conesearch. used by table_generate.ipynb to generate unique conesearch scripts
```

## BCG List

The work on cleaning up the BCG list primarily consisted of classifying each BCG based on the outline below, fixing the citations for some of the clusters, updating entries for some of the clusters, and fixing some miscellaneous issues such as incorrectely listed coordinates. Within the final BCG list ('full_BCG_list.xlsx'), entries that were updated have red text.

Classification System:
1 = reasonably certain BCG/s with spec z
2 = good candidate BCG/s with phot z, needs follow-up spec z
3 = BCG strong candidate based on appearance and color (e.g. HST imaging), but needs follow up spec z
4 = ambiguous BCG, multiple candidates, 1-2 listed here, may need better imaging before spec z
5 = no candidates for BCG very ambiguous, no coordinates to list
For clusters with more than one BCG, an asterisk was added after the number, i.e. 1*
Some clusters have an 'x' instead of a number, as these are currently awaiting review from Professor Donahue

In addition to fixing this list, I have performed some rudimentary. The file 'bcg_coords_compare.csv' includes the angular separation of each BCG from the cluster center as well as the difference in redshift. With this I produced a graph of angular separation versus entropy for clusters with entropy profiles:

![angular sep vs entropy graph](https://github.com/farahconor/summer23/blob/main/BCGs/bcg_separation_vs_k0/bcg_separation_vs_k0.png)

The file structure is detailed below:

```
BCGs
 ┣ bcg_separation_vs_k0
 ┃ ┣ bcg_separation_vs_k0.ipynb: a jupyter notebook that generates a plot of angular separation vs k0
 ┃ ┗ bcg_separation_vs_k0.png: the generated graph saved as a png
 ┣ bcg_coords_compare
 ┃ ┣ bcg_coords_compare.ipynb: a jupyter notebook that calculates the angular separation and redshift difference for each BCG and creates a table
 ┃ ┗ bcg_coords_compare.csv: the saved table produced from the script above, including cluster name, angular separation, z difference, and k0
 ┣ full_BCG_list.xlsx: the full list of BCGs, saved as an excel file
 ┗ BCG_bibtex.txt: all bibtex citations from the full_BCG_list.xlsx
 ```

 ## Website

 As of now, the website consists of a MC2 homepage with a list of projects which right now lists ACCEPT1 (which is just a link to the old accept website) and ACCEPT2. ACCEPT2 is then stored entirely within a folder, which can allow you to add additonal projects to the MC2 site without having them interfere. ACCEPT2 then has it's own homepage (I still need to add information about the project). There is then a page called "Cluster Index" which includes a list of each cluster, its coordinates, and whether or not it has global properties, a profile, morphology, and in ACCEPT1. Clusters with profiles have a link to a page called "Cluster Figures" which will automatically pull the T, Z, and K graphs for that cluster. These graphs are stored within the folder called "figuresets", which has the same file structure as the ACCEPT2 folder that was given to me. For the global properties table, there is a folder that includes a cascade page for the global properties table, a non-cascade fullscreen HTML table, a raw MRT table, the raw csv, and the VOtable conesearch. For now this is the only table that is on the website. But using the tools detailed in the earlier section, additional tables can be easily created and added to cascade.