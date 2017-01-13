A Thousand Words
================

"A Thousand Words" was a community site where users post short stories inspired by user-submitted pictures. I made for my wife, but it never took off. It should be fully functional, but it was written a long time ago so the quality of the code is probably somewhat lacking.

Development
-----------

The development of the site was detailed in a series of blog posts:

1. [a thousand words: A New Timesink has Arrived!](https://ianrenton.com/blog/a-new-timesink-has-arrived/)
2. [a thousand words: First Sketches](https://ianrenton.com/blog/a-thousand-words-first-sketches/)
3. [a thousand words: GETting and POSTing](https://ianrenton.com/blog/a-thousand-words-getting-and-posting/)
4. [a thousand words: Hot Profilin' Action](https://ianrenton.com/blog/a-thousand-words-hot-profilin-action/)
5. [a thousand words: Alpha, Beta](https://ianrenton.com/blog/a-thousand-words-alpha-beta/)
6. [a thousand words: Finishing Touches](https://ianrenton.com/blog/a-thousand-words-finishing-touches/)

Install on Heroku
-----------------

* Set up a MySQL database somewhere
* Run:

```
    git clone https://github.com/ianrenton/athousandwords.git
    cd athousandwords
    cp sample.env .env
```
* Edit `.env` in your favourite editor
* Run:

```
    heroku apps:create
    heroku config:push
```
Since Heroku is read-only, uploading pictures involves adding them to the repo, committing it and pushing it. It would be nice in future to support automatic upload to Amazon S3 or something.
