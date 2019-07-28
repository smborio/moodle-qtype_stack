# Authoring quick start 2: multi-part mathematical questions

This is the second part of the [authoring quick start](Authoring_quick_start.md).  The purpose is to write multi-part questions.

### Example 1 ###

Find the equation of the line tangent to \(x^3-2x^2+x\) at the point \(x=2\).

1. Differentiate \(x^3-2x^2+x\) with respect to \(x\).
2. Evaluate your derivative at \(x=2\).
3. Hence, find the equation of the tangent line. \(y=...\)

Since all three parts refer to one polynomial, if randomly generated questions are being used then each
of these parts needs to reference a single randomly generated equation. (I would not mention randomisation at this stage as it has not been discussed at all yet) Hence parts 1.-3. really form
one item.  Notice here that part 1. is independent of the others. Part 2. requires both the first and second inputs.
Part 3. could easily be marked independently, or take into account parts 1 & 2. Notice also that the teacher may
choose to award "follow on" marking.

### Example 2 ###

Consider the following question, asked to relatively young school students.

Expand \((x+1)(x+2)\).

In the context it is to be used it is appropriate to provide students with the
opportunity to "fill in the blanks", in the following equation.

    (x+1)(x+2) = [?] x2 + [?] x + [?].

We argue this is really "one question" with "three inputs".
Furthermore, it is likely that the teacher will want the student to complete all boxes
before any feedback is assigned, even if separate feedback is generated for each input
(i.e. coefficient). This feedback should all be grouped in one place on the screen. Furthermore,
in order to identify the possible causes of algebraic mistakes, an automatic marking procedure
will require all coefficients simultaneously. It is not satisfactory to have three totally
independent marking procedures.

These two examples illustrate two extreme positions.

1. All inputs within a single multi-part item can be assessed independently.
2. All inputs within a single multi-part item must be completed before the item can be scored.

Devising multi-part questions which satisfy these two extreme positions would be relatively straightforward.
However, it is more common to have multi-part questions which are between these extremes, as in the case of our first example.

### Response processing ###

Response processing is the means by which a student's answer is evaluated and feedback, of various forms,
assigned. The crucial observation in STACK is a complete separation between two important components:

1. a list of [inputs](Inputs.md);
2. a list of [potential response trees](Potential_response_trees.md).

## [Inputs](Inputs.md) ##

The [question text](CASText.md#question_text), i.e. the text actually displayed to the student, may have an arbitrary number of [inputs](Inputs.md). An element may be positioned
anywhere within the question text, including within mathematical expressions, e.g. equations (_note_: MathJax currently does not support form elements within equations). (I don't think the note here adds any value and new authors won't know what you are talking about, what is MathJax for exaple?)
Each input will be associated with a number of fields. For example

1. The name of a CAS variable to which the student's answer (if any) is assigned during response processing.
   This could be automatically assigned, e.g. in order `ans1`, `ans2`, etc. Each variable is known as an answer variable.
2. The type of the input. Examples include
  1. direct linear algebraic input, e.g. `2*e^x`.
  2. graphical input tool, e.g. a slider.
  3. True/False selection.
3. The teacher's correct answer.
(The numbering is viewed horribly in github, I'm not sure it's the same for the website. I would if possible change it to 1. 2. a. b. c. 3. )

## [Potential response trees](Potential_response_trees.md) ##

A potential response tree (technically an acyclic directed graph) consists of an arbitrary number of linked nodes
we call potential responses. In each node two expressions are compared using a specified Answer Test,
and the result is either TRUE or FALSE. A corresponding branch of the tree has the opportunity to

1. Adjust the score, (e.g. assign a value, add or subtract a value);
2. Add written feedback specifically for the student;
3. Generate an "Answer Note", used by the teacher for evaluative assessment;
4. Nominate the next node, or end the process.

Each question will have zero or more potential response trees. For each potential response tree there will be the following.

1. A maximum number of marks available, i.e. score.
2. A list of which answer variables are required for this response tree. Only when a student has
   provided a **valid** response to all the elements in this list will the tree be traversed and outcomes assigned.
3. A collection of potential response variables, which may depend on the relevant answer variables, question
   variables and so on. These are evaluated before the potential response tree is traversed.
4. A nominated point in the question itself into which feedback is inserted.
   This feedback will be the mark, and textual feedback generated by this tree.

Permitting zero potential response trees is necessary to include a survey question which is not
automatically scored. An input which is not used in any potential response tree is
therefore treated as a survey and is simply recorded.

To illustrate multi-part mathematical questions we start by authoring an example.  We assume you have just worked through the [author quick start guide](Authoring_quick_start.md) so that this is somewhat abbreviated.

## Authoring a multi-part question ##

Start with a new STACK question, and give the question a name, e.g. "Tangent lines".  This question will have three parts.  Start by copying the question variables and question text as follows.  Notice that we have not included any randomization, but we have used variable names at the outset to facilitate this.

__Question variables:__

     p:x^3-2*x^2+x;
     pt:2;
     ta1:diff(p,x);
     ta2:subst(x=pt,diff(p,x));
     ta3:remainder(p,(x-pt)^2);

__Question text__

This text should be copied into the editor in HTML mode. (Why HTML mode here? In the github viewer this does not appear as HTML text but normal text and when you copy and paste it into HTML mode it doesn't look as expected)

<textarea readonly="readonly" rows="5" cols="120">
<p>Find the equation of the line tangent to {@p@} at the point \(x={@pt@}\).</p>
<p>1. Differentiate {@p@} with respect to \(x\). [[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</p>
<p>2. Evaluate your derivative at \(x={@pt@}\). [[input:ans2]] [[validation:ans2]] [[feedback:prt2]]</p>
<p>3. Hence, find the equation of the tangent line. \(y=\)[[input:ans3]] [[validation:ans3]] [[feedback:prt3]]</p>
</textarea>

Fill in the answer for `ans1` (which exists by default) and remove the `feedback` tag from the "specific feedback" section.  We choose to embed feedback within parts of this question.
Notice there is one potential response tree for each "part".

Update the form and then ensure the Model Answers are filled in as `ta1`, `ta2` and `ta3`. (for the first time a user is doing a multi part question, I think the "verify question text and update form" button needs more emphasys. Also, I would say Modal Answers in the Input section or something like that for further clarity.)

STACK has created the three potential response trees by detecting the feedback tags automatically.  Next we need to edit potential response trees.  These will establish the properties of the student's answers.

###Stage 1: getting a working question###

The first stage is to include the simplest potential response trees.  These will simply ensure that answers are "correct".  In each potential response tree, make sure that \(\mbox{ans}_i\) is algebraically equivalent to \(\mbox{ta}_i\), for \(i=1,2,3\).  At this stage we have a working question.  Save it and preview the question.  For reference the correct answers are

     ans1 = 3*x^2-4*x+1
     ans2 = 5
     ans3 = 5*x-8

###Stage 2: follow-through marking###

Next we will implement simple follow through marking.

Look carefully at part 2.  This does not ask for the "correct answer" only that the student has evaluated the expression in part 1 correctly at the right point.  So the first task is to establish this property by evaluating the answer given in the first part, and comparing with the second part.  Update node 1 of `prt2` to establish the following.

    AlgEquiv(ans2,subst(x=pt,ans1))

Next, add a single node (to `prt2`), and ensure this node establishes that

    AlgEquiv(ans1,ta1)

We now link the true branch of node 1 to node 2 (of `prt2`).  We now have three outcomes.

Node 1: did they evaluate their expression in part 1 correctly? If "yes" then go to node 2, else if "no" then exit with no marks.

Node 2: did they get part 1 correct?  if "yes" then this is the ideal situation, full marks.  If "no" then choose marks, as suit your taste in this situation, and add some feedback such as the following in Node 2, false feedback.

<textarea readonly="readonly" rows="5" cols="120">
Your answer to this part is correct (It's not correct, it follows your answer from part 1 correctly), however you have got part 1 wrong!  Please try both parts again!
</textarea>
(This looks funny in the github veiwer as well, I don't know what happens when in the website)

###Stage 3: adding question tests### (I didn't understand this section. I think more detail on the purpose of "question tests and deployed versions" is needed here or somewhere in the authoring guide.)

It is probably sensible to add question tests.  From the question preview window, click on `Question tests & deployed versions` link in the top right of the page.

Add a test to your question which contains the correct answers, as follows.

     ans1 = 3*x^2-4*x+1
     ans2 = 5
     ans3 = 5*x-8

The marks should all be "1" and the answer notes as follows.

     prt1 = prt1-1-T
     prt2 = prt2-2-T
     prt3 = prt3-1-T


Now add a new answer test to check the follow-through marking.  For example, make the following mistake in part 1, but use it in part 2 correctly.

     ans1 = 3*x^2-4
     ans2 = 8
     ans3 = y=8*x-8

The marks should all be "0" and the answer notes as follows.

     prt1 = prt1-1-F
     prt2 = prt2-2-F
     prt3 = prt3-1-F

When you run the tests you can also look at the feedback to confirm the system is giving the kind of feedback you want for these types of mistake.

###Stage 4: Random question### (this is up to you but I would not personally start randomising things like this, I would have a chapter on randomising. This adds a layer of complexity to the multi-part question which I don't think is necessary. You are already adding complexity by using variables without adding a lot of detail as to why or the advantage of doing so. If you want to include randomisation here, I would write a paragraph explaining why we used variables in the first place.)

Next we can add a randomly generated polynomial to the question.  Because we used variable names throughout the question from the start, this should be a simple matter of redefining the value of `p` in the question variables as follows.

    p : (2+rand(3))*x^3+(2+rand(3))*x^2+(2+rand(3))*x;

You will need to add a non-empty question note to enable grouping of random versions.(I would specify where more clearly)  E.g. the following string will suffice.

    {@p@}

We now need to update the question tests to reflect this.  In the first test, you are free to use `ta1` etc. to specify the correct answers.

In the second test you might as well leave the test as is.

# Next steps #

* You might like to look at the entry for [feedback](Feedback.md).
* You might like to look at more information on [Maxima](../CAS/index.md), particularly the Maxima documentation if you are not very familiar with Maxima's syntax and function names. A graphical Maxima interface like [wxMaxima](http://andrejv.github.com/wxmaxima/) can also be very helpful for finding the appropriate Maxima commands easily.

The next part of the authoring quick start guide looks at [turning simplification off](Authoring_quick_start_3.md).

