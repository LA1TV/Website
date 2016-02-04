# API
- Our api is accessible under "https://www.la1tv.co.uk/api/v1".
- All responses from the api are json.
- By default responses are pretty printed but you can append "?pretty=0" to disable this.
- If an error occurs for some reason or something is unavailable a http status code other than 200 will be returned and we can no longer guarantee a json response if this is the case. For example, if we go into maintenence mode you will get a 503.
- To access the api you need to provide a valid api key in a "X-Api-Key" header. To get an api key please contact us.

## Endpoints
- All of the following urls should be appended to "https://www.la1tv.co.uk/api/v1".
- All responses are json objects contained under a "data" key.

|   URL     | Info            |
|-----------|------------------|
| /service  | Information about the service.
| /permissions | Information about the permissions you've been assigned.
| /playlists| All of the playlists in the system. This can contain playlists that belong to shows. Playlists that belong to a show will have the "show" property set with an object containing the show id and the series number.
| /playlists/{id} | Information about a specific playlist and the media items it contains.
| /playlists/{id}/mediaItems | Information about the media items that a specific playlist contains.
| /playlists/{id}/mediaItems/{id} | Information about a specific media item that is part of a specific playlist.
| /shows    | All of the shows in the system.
| /shows/{id} | Information about a specific show and the playlists it contains which represent the series of the show.
| /shows/{id}/playlists | Information about the playlists that a specific show contains.
| /mediaItems | Information about the media items that are returned as a result of the query parameters.<ul><li>**?limit=X:** X is the maximum number of items to retrieve.</li><li>**?sortMode=X:** X is "POPULARITY" or "SCHEDULED_PUBLISH_TIME".</li><li>**?sortDirection=X:** X is "ASC" or "DESC".</li><li>**?vodIncludeSetting=X:** X is "VOD_OPTIONAL", "HAS_VOD", "HAS_AVAILABLE_VOD" or "VOD_PROCESSING". Note "VOD_PROCESSING may only be used with sortMode "SCHEDULED_PUBLISH_TIME".</li><li>**?streamIncludeSetting=X:** X is "STREAM_OPTIONAL", "HAS_STREAM" or "HAS_LIVE_STREAM".</li></ul>
| /mediaItems/{id} | Information about a specific media item and the playlists it is in.
| /mediaItems/{id}/playlists | Information about the playlists that a specific media item is contained in.


## Real Time Events With A Webhook
You can also receive real time events using our webhook support.
Our server will make a HTTP POST request to a URL provided by you.

The request with have content type `application/json` and will contain JSON in the body in the following form:
```
{
	"eventId":<<EVENT ID>>,
	"payload":<<EVENT PAYLOAD>>,
	"time":<<unix time in milliseconds when the event was sent>>
}
```

The following table shows the different events and the structure of their payloads.

|  Event ID        |  Payload Structure       |  Info    |
|------------------|--------------------------|----------|
| mediaItem.live   | `{id:<<Media Item Id>>}`   | This event occurs when a media item goes to the "Live" state.
| mediaItem.showOver | `{id:<<Media Item Id>>}` | This event occurs when a media item goes to the "Show Over" state.
| mediaItem.notLive | `{id:<<Media Item Id>>}`  | This event occurs when a media item goes to the "Not Live" state, which may occur if a show has to restart for some reason.
| mediaItem.vodAvailable | `{id:<<Media Item Id>>}`  | This event occurs when VOD for a media item becomes available to watch.
| test              | `{info:"Hello!"} `        | This is a test event which you can trigger yourself (see below).

### Registering Your URL
To register the URL which you would like to use to receive the events at, make a POST request to "https://www.la1tv.co.uk/api/v1/webhook/configure" with the URL set to a key "url". If succesful you will receive a response with status code 200 and your URL will be shown back to you.

You need to provide your api key in an "X-Api-Key" header with this request.

To remove your URL perform the above but leave the URL as an empty string.

### Testing
You can simulate the "test" event by making a POST request to "https://www.la1tv.co.uk/api/v1/webhook/test" along with your api key in an "X-Api-Key" header.

If the event was dispatched a response with status code 200 will be returned, and the "success" property in the response will be `true`.

## Contact Us
If you have any questions about the api please contact us at the "Technical Support" address listed on the [contact page](https://www.la1tv.co.uk/contact).
