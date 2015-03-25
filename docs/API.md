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
| /playlists| All of the playlists in the system. This can contain playlists that belong to shows. Playlists that belong to a show will have the "show" property set with an object containing the show id and the series number.
| /playlists/{id} | Information about a specific playlist and the media items it contains.
| /playlists/{id}/mediaItems | Information about the media items that a specific playlist contains.
| /playlists/{id}/mediaItems/{id} | Information about a specific media item that is part of a specific playlist.
| /shows    | All of the shows in the system.
| /shows/{id} | Information about a specific show and the playlists it contains which represent the series of the show.
| /shows/{id}/playlists | Information about the playlists that a specific show contains.

## Contact Us
If you have any questions about the api please contact us at the "Technical Support" address listed on the [contact page](https://www.la1tv.co.uk/contact).