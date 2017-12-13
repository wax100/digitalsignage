# README #
Make sure this file is not deployed by DeployHQ.

### Project details ###

|                       |                                                                    |
|-----------------------|--------------------------------------------------------------------|
| **Summary**           | Write a summary about this project here.                           |
| **Production**        | https://demo.sterc.com                                             |
| **Project Manager**   | -                                                                  |
| **Lead Developer**    | Oene Tjeerd de Bruin                                               |
| **Developer**         | Oene Tjeerd de Bruin                                               |
| **JIRA Board**        | https://stercbv.atlassian.net/secure/RapidBoard.jspa?rapidView=33&view=detail&selectedIssue=XS-210&quickFilter=264 |
| **Development branch**| development                                                        |

### TinymceWrapper ###

To make TinymceWrapper work with Digital Signage change the following TinymceWrapper plugin properties:

```
customJS: true
customJSchunks: DigitalSignage
```

After that create a chunk with the name 'TinymceWrapperDigitalSignage' and the following code:

```
MODx.loadRTE = function(id, config) {
    tinymce.init(Ext.applyIf({
        selector: '#' + id,
        [[$TinymceWrapperCommonCode]],
        setup: function(editor) {
            editor.on('init', function(e) {
                e.target.save();
            }).on('change', function(e) {
                e.target.save();
            });
        }
    }, config))
};
```