# MyCareer
A complex machine learning algorithm to classify individuals based on gathered data.

The files that do the grunt work are:

  - table.php [1-5] running in parallel
  - classify.js, careers.js, and result.js
  - data.php, process.php, careers.php, career.php

Data sources used:
  - CSV provided for the challenge
  - O*NET Database for interests, skills, etc...
  
Overview of the algorithm and logic behind classification of careers:
> The algorithm takes O*NET database Levels from either 1 to 7 or 0 to 7 and creates a simple linear regression based on the distance from the user's inputs and the values stored in the database. Therefore, an Interest in Science of importance 7 for a Biologists has a difference of 3 is the user inputs a 4 level of interest in biology. Then the values are averaged.

This process of averaging out each category, interests, knowledge, skills, and abilities allows each area to be equally weighted. Finally a total average is taken from all of these categories.

> After the simple linear regression, the algorithm employs Bayes Theorem machine learning. It operates under the assumption that the traits of gender, degree, major, and generation of the person are independent when it comes to a career. Furthermore, the Bayes is applied on top of the simple linear regression.

Calculate Major P(Profession | Evidence) = P(Evidence | Profession) x P(Profession) 
Utilize P(c|x) = P(Xo|c) x P(X1|c) ... x P( c )

[Prob(Profession | Major) x Prob(Gender) x Prob(Degree) x Prob(Age)]/[Average Aggregate AverageDifference in Each Category]  ==> High prob divide by small number creates big classification curve linearly regressed, if denominator is 0, it is changed to (.01 x categories)


>This allows for a strong curve to be drawn in order to classify the individual. A high posterior probability that a user will be assigned to the profession is divided by their interests. The closer their interests are to the data the lower the number. Therefore, a high probability divided by a small number creates a huge jump making classification much more accurate.

This is the best implementation method because MyCareer uses many methods to make classification fast and more accurate. Firstly, the data derived from Education based on the given data is given a pseudo count of one to remove all possible 0 probabilities. Then the data is smoothed additively using Laplace smoothing. This allows for a more accurate induction to be made on the data.

Next, the data is parsed into sql database using table.php. Here MyCareer smooths the data and aggregate totals for probability calculations in process.php.  

The user profile is then derived from Mean of the Averages of each area. Since we employ linear regression and Bayes Theorem, the application avoids n-dimension curse because n-dimension space with these many categories would be entirely too large to create conclusions on as there would be so much space given the 1000+ majors, and 30+ skills, knowledge areas, and abilities. Thus, SVM or other classification methods would not be as accurate and much slower.

### Codebase

Dillinger uses a number of open source projects to work properly:

* [Skel] - HTML enhanced for web apps!
* [BootStrap 3] - Framework for easy inputs
* [sklearn] - Python machine learning open-source library
* [jQuery] - For enhanced javascript functionality

All files are available on GitHub as well as sklearn library build.

### Status

Scripts are running to parse all CSV into sql and smooth the data and prepare it for the algorithm. These scripts are being ran in parallel to populate all majors. At the moment the site works perfectly fine, just the Probability of professions is skewed based on professions who employ currently populated majors more than other professions. This will fix itself as all careers are populated.

### Pages

index.html
 >Explore the application by coming to GitHub, searching careers, or trying the classification method.
 
careers.html
>A page that can be populated using URL variables by method GET. Searches the career and descriptors from O*NET database to show user more information. Shows user important areas of knowledge, skills, and abilities. As well as, showing related professions they can transition into or are similar to start off with.

classfiy.html
> Enter user input to determine the career based on the algorithm. Enter General information such as birthdate and gender. Enter education information such as degree and level of degree. Then rate your level of interests, skills, abilities, and knowledge to find your career.

result.html
> Final destination shows, top 5 results from process.php. Clicking these opens new window to view more information.

### Codebase
##### PHP Files
data.php
> populates information such as sliders on classify.html dynamically

careers.php
> Populates form to choose career on careers.html

career.php
> Searches all information of career on O*NET and displays it to user.

process.php
> Algorithm main function to linear regression based on user profile and Bayes Theorem on Probability of Education, Gender, Generation, and Degree Level. Final output is a redirect to results.html populated with URL variables for easy sharing.

table.php
> Multiple that splits columns for Majors up in sub groups to process all 1536. Currently running as of 8/16/2016. Laplace smoothes data with a pseudocount of one. Performs other aggregations to allow for fast process.php time.

##### Javascript Files
careers.js
>Read GET URL variables and display proper table formatting.

classify.js
>Serialize form, show loading bar, and create inputs.

result.js
>Read GET URL variables and display in 1-5 list with links to proper pages.

#### Questions?

Any questions, comments, or concerns email me at pgregotski@gmail.com and check out http://projects.miscthings.xyz/ for more cool projects.

### Sources Used For Research

https://en.wikipedia.org/wiki/Additive_smoothing
https://en.wikipedia.org/wiki/Pseudocount
https://en.wikipedia.org/wiki/Prediction_by_partial_matching
https://en.wikipedia.org/wiki/Bayes_classifier
https://en.wikipedia.org/wiki/Naive_Bayes_classifier
https://en.wikipedia.org/wiki/Curse_of_dimensionality

https://www.analyticsvidhya.com/blog/2015/08/common-machine-learning-algorithms/
https://azure.microsoft.com/en-us/documentation/articles/machine-learning-algorithm-choice/


