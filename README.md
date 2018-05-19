# FreshWeb Carddeck Plugin for the FreshWeb Theme

This hand-coded plugin creates, what we call, a 'card deck' that display photos of various sizes. It's useful for displaying a portfolio of screenshots of client websites. As the user scrolls the card deck into view, each of the photos will increase it's opacity to its fullest, giving each photo its due focus to the user. It's a neat effect.

This plugin is currently used on the home page of the [freshwebstudio.com](https://freshwebstudio.com) website, if you care to see it in action.

# Plugin Ownership

If you're reading this file, it probably means that you are either responsible for the development of this plugin, inquisitive, or you have a lot of time on your hands. Let's assume that you're a web developer and are now responsible for future develpment or content changes in this plugin. Hopefully, we at [FreshWeb Studio](https://freshwebstudio.com), the developers of this plugin, have created this plugin sufficiently to make your job easier.

This document is intended just for you, to provide you with the development details of this plugin and instructions for maintaining it.

# Development Guide

Read this section carefully before modifying the plugin code. Here's an outline:

    1. Git Version Control System
        -- Starting From Scratch
        -- Import Existing Files on Your Computer
        -- Tagging
        -- Git Files
    2. CSS Guide
        - BEM Naming Convention
    And That's It!

## 1. Git Version Control System

[Git](https://git-scm.com/book/en/v2/Getting-Started-Git-Basics) is a version control system for tracking changes in computer files and coordinating work on those files among multiple people. The development of this plugin uses Git for this purpose. More specifically, we use [Beanstalk](https://beanstalkapp.com) as our public Git repository.

We will show you a couple of common ways to start using Git, with [Beanstalk as an example repository(https://support.beanstalkapp.com/article/848-getting-started-with-git-creating-your-repository).

**Developer Tip:** If you're looking for a public Git repository, consider [Beanstalk](https://beanstalkapp.com/). It provides many automated features, including deploying your code to production with a push of a button.

### Starting From Scratch

To start using your repository from scratch, on your command line type the following:

```
cd /wp-content/plugins
git clone https://freshwebstudio.git.beanstalkapp.com/freshweb-plugin-freshweb-carddeck.git -o beanstalk freshweb-carddeck
cd freshweb-carddeck
echo "Append a line to my README.md file." >> README.md
git add README.md
git commit -m "My first commit."
git push beanstalk master
```

With the commands above, you will create a folder, download the plugin from Git, modify a file in it, make your first commit, and push the changes to your repository, to master branch. Master branch is the default branch to use for your files.

### Import Existing Files on Your Computer

To import your existing files from your local machine type the following in your command line:

```
cd /wp-content/plugins/freshweb-carddeck
git init
git remote add beanstalk git@freshwebstudio.beanstalkapp.com:/freshweb-plugin-freshweb-carddeck.git
git add .
git commit -m "Importing my project to Git, without saving history."
git push beanstalk master
```

### Tagging

You can also tag commits to represent plugin versions after you commit your changes. Suppose your commit history looks like this:

```
$ git log --pretty=oneline
7a14d966d750a3ae24d68bdd80849a6fccc13d22 (HEAD -> master, beanstalk/master, beanstalk/HEAD) Updated version.
da74cfe37fa6c6394bb9ba4553cf031377193a6b Did more stuff.
2f42bb4b20002306f2d08ad31cc3f83c1f9ca849 Did stuff.
...
```

Now, tag the project at v1.0.1, which was at the HEAD commit. To tag that commit, you specify the commit checksum (or first 7 characters) at the end of the command:

```
$ git tag -a v1.0.1 7a14d96

# By default, the git push command doesn’t transfer tags to 
# remote servers. You will have to explicitly do this.
$ git push beanstalk v1.0.1
```

Now you can see the tagged commit:

```
$ git log --pretty=oneline
7a14d966d750a3ae24d68bdd80849a6fccc13d22 (HEAD -> master, tag: v1.0.1, beanstalk/master, beanstalk/HEAD) Updated version.
da74cfe37fa6c6394bb9ba4553cf031377193a6b Did more stuff.
2f42bb4b20002306f2d08ad31cc3f83c1f9ca849 Did stuff.
...
```

If you mistakenly tag a commit, you can undo this by deleting the tag:

```
# Delete from remote repository
$ git push --delete beanstalk v1.0.1

# Delete from local repository
$ git tag -d v1.0.1

```

### Git Files

As a result of using Git, you may see the following files in the root project folder:

```
/freshweb
+-- /.git       // Local Git repository
+-- .gitignore  // List folders/files Git should ignore

```

As long as FreshWeb Studio manages your website, we'll keep your plugin in a Git repository. This allows us to track changes and deploy the correct production versions. It is highly recommended that if you inherit responsibility for the development of this plugin, you continue to use Git or other version control system as your repository.

**Warning:** If FreshWeb Studio is managing your website, then DO NOT edit the files located on your web server! The plugin files located in our Git repository are the source files where ALL website changes must be made. If you change the web server files, then when we deploy development changes to your web server, we will thus overwrite your edits. Please request all changes through us. Note that this is not an effort by us to keep your files ransomed, but it's necessary to have a single source of changes to your website files as long as we are responsible for them.

Learning and using Git is beyond the scope of these instructions. [Get started with Git basics](https://git-scm.com/book/en/v2/Getting-Started-Git-Basics).

## 2. CSS Guide

If this plugin includes one or more stylesheets, then there are some CSS style guide practices and conventions that you should continue to adhere to if you're going to develop this plugin. If you are tempted by shortcuts, please refrain. Following the conventions below will ensure that the plugin is maintainable and consistent.

### BEM Naming Convention

We've chosen to use the [BEM naming convention](http://getbem.com/naming/) for organizing and naming new CSS rule selectors. By structuring the HTML DOM elements with class names using this convention, the code is much easier to edit, maintain, and less error-prone.

A BEM class name includes up to three parts.

1. Block: The outermost parent element of the component is defined as the block.
2. Element: Inside of the component may be one or more children called elements.
3. Modifier: Either a block or element may have a variation signified by a modifier.

If all three are used in a name it would look something like this:

```
[block]__[element]--[modifier]
```

Here's a quick example:

```
------- DO THIS -------
<figure class="photo">
  <img class="photo__img" src="me.jpg">
  <figcaption class="photo__caption">Look at me!</figcaption>
</figure>

<style>
  .photo { } /* Specificity of 10 */
  .photo__img { } /* Specificity of 10 */
  .photo__caption { } /* Specificity of 10 */
</style>

------- DON'T DO THIS -------
<figure class="photo">
  <img src="me.jpg">
  <figcaption>Look at me!</figcaption>
</figure>

<style>
  .photo { } /* Specificity of 10 */
  .photo img { } /* Specificity of 11 */
  .photo figcaption { } /* Specificity of 11 */
</style>
```

One of the purposes behind BEM is to keep specificity low and consistent. Don’t omit class names from the child elements in your HTML. That will force you to use a selector with increased specificity to style those bare elements inside the component. Leaving those classes off may be more succinct, but you will increase risks of cascade issues in the future. One goal of BEM is for most selectors to use just a single class name.

# And That's It!

We hope these instructions are helpful if you find yourself maintaining the development of this plugin. 

David Faltermier, March 26, 2018. [FreshWeb Studio](https://freshwebstudio.com)

