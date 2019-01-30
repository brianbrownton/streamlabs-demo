# STREAMLABS demo/assignment
>The first/home page lets a user login with Twitch and set their favorite Twitch streamer name. This initiates a backend event listener which listens to all events for given streamer.

>The second/streamer page shows an embedded livestream, chat and list of 10 most recent events for your favorite streamer. This page doesn’t poll the backend and rather leverages web sockets and relevant Twitch API.

This repository fulfills those objectives, mostly. The 10 most recent events cannot currently be done as there is no API for past events, and even (as I show below) the current implementation of future events is not officially supported. Further, I don't see why either polling or websockets would be required for this functionality. If there is another type of event that is being referred to here (as event can be fairly generic), it is not clear. I am using Twitch's meaning of the word: https://help.twitch.tv/customer/en/portal/articles/2777118-how-to-use-events

Also, I do not believe a user is actually required to login for the requested functionality (although I have implemented it as asked). Finding a streamer and listing their events does not require a twitch login and can be done just with a dev account and associated `client_id` I believe.

## INFO
### events api
This is not an officially supported API and probably shouldn't be relied upon as such.
( https://discuss.dev.twitch.tv/t/events-api-past-events/11543, https://discuss.dev.twitch.tv/t/post-events-via-twitch-api/17655, https://discuss.dev.twitch.tv/t/events-in-the-new-twitch-api/13335/4 )

There does not seem to be a known way to get PAST events, however some of the above links show a way to fetch future events (which I have done).

## USAGE
Checkout the repository to a host with PHP7+ and `composer install`. I personally used the docker image `trafex/alpine-nginx-php7` to speed through this.

## QUESTIONS
per https://gist.github.com/osamakhn/14a378f3107d49de47e0b617a3d5fdf5

>How would you deploy the above on AWS? (ideally a rough architecture diagram will help)

The project is build with docker, so it should be an easy deploy anywhere (eg. `docker-compose up`) Currently it is just one image/container with runs both PHP and nginx which should probably be split into two at the very least for a production deployment. This can then be independently scaled with AWS ECS/ECR and a load balancer until you run out of money or traffic.

```
              /> SERVER A
TRAFFIC -> LB -> SERVER B
              \> SERVER C
```

>Where do you see bottlenecks in your proposed architecture and how would you approach scaling this app starting from 100 reqs/day to 900MM reqs/day over 6 months?

The question is worded strangely, as I'm not sure what 6 months has to do with it... if you need  something to be able to handle 900M reqs/day it shouldn't really matter if it's only for a day or indefinitely. As I stated in the answer to the previous question, I believe the architecture laid out would be fine until you ran out of traffic or money. To be more precise, however, in any deployment while scaling I would look at where the bottlenecks are and then figure out a way to alleviate them. There is no DB here, so we are only dealing with 3 things: the LB, the PHP application, and the webserver (nginx here). If one of them started having issues I would have to either add more to the pool (horizontal scaling), or use bigger boxes (vertical scaling).

The twitch API is also rate limited to 800 reqs/minute (1,152,000/day). Even assuming even distributed traffic by time, since 900M reqs/day > 1.152M reqs/day you would either need to ask twitch for a higher rate limit or pool about 900+ API keys and balance the load between them.

I would also profile the code and rewrite it in a more efficient manner or in a more efficient language to save on costs.