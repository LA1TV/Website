# Embeddable Player
Our embeddable player lives at "https://embed.la1tv.co.uk" and has the structure of "https://embed.la1tv.co.uk/{playlist id}/{media item id}" for media items, and "https://embed.la1tv.co.uk/livestream/{live stream item id}" for live streams. It should be used inside an iframe and it is recommended that you add the following attributes to the iframe tag:
- frameborder="0"
- allowfullscreen
- webkitallowfullscreen
- mozallowfullscreen

You can get an example embed code by clicking on the "share" button under the player on the site.

**Note:** For the media items url you can omit the playlist id and this means the system will automatically choose a playlist that the media item is contained in.

## Parameters
There are quite a few url paramaters that can be provided to contol it's behaviour which are provided below along with there default values.
- **autoPlayVod**: Defaults to "0". Set to "1" to cause vod to automatically play.
- **autoPlayStream**: Defaults to "0". Set to "1" to automatically start live streams.
- **vodPlayStartTime**: Defaults to "". Set to "XmYs" to start vod playback from a specific point.
- **flush**: Defaults to "1". Enables flush mode which means the player fills the frame without any margins. If "0" the bottom bar will be available which contains the like button and view count, along with quality selection.
- **showHeading**: Defaults to "0". Show the title of the media item above the player. Only valid when "flush" is "0".
- **hideBottomBar**: Defaults to "0". Hides the bottom bar which sits below the player. Only valid when "flush" is "0".
- **ignoreExternalStreamUrl**: Defaults to "0". When set to "1" any streams that are set to be hosted externally from the main website will be shown in the player instead of a button to go to the external site.
- **disableFullScreen**: Defaults to "0". When set to "1" the full screen button is removed from the player.
- **vodQualityId**: Defaults to "". This can be set to the id of a quality and then if this quality is available it will become the initial quality for the vod.
- **streamQualityId**: Defaults to "". This can be set to the id of a quality and then if this quality is available it will become the initial quality for the stream.
- **kiosk**: Defaults to "0". If set to "1" this will overide the auto play options forcing them to be enabled. It will also force the flush player and enable the "ignoreExternalStreamUrl" option. It disables any user interaction with the player.

## API
The embeddable player also has an api which you can access using html5 messages. The iframe emits messages where the data is a json string which is of the form:

    {
      playerApi: {
        eventId: <an event id>,
        state: {
          type: <either "ad", "vod", or "live">,
          playing: <true or false>
        }
      }
    }
    
These are the event ids that are broadcast:
- **typeChanged**: When the player type changes between "ad", "vod" and "live".
- **play**: When the player enters the "playing" state.
- **pause**: When the player enters the "paused" state.
- **ended**: When the player reaches the end of the video in the case of "vod", or the end of the live stream in the case of "stream".
- **stateUpdate**: Whenever the state is requested or it is being sent for the first time after the iframe has loaaded.

You can send certain commands to the player to control it by posting a message of the following structure (as a json string) to the iframe.

    {
      playerApi: {
        action: <action>
      }
    }
  
  These are the actions you can provide:
  - **PLAY**: This mimics the user clicking the play button.
  - **PAUSE**: This mimics the user clicking the pause button.
  - **STATE_UPDATE**: This requests for the state to be broadcast with the "stateUpdate" event id. Useful to get the state initially if you miss when it is first broadcast.
