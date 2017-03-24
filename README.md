# FeedMe-Helpers
A collection of helper plugins for Feed Me to support third-party ElementTypes and FieldTypes.

### Installation
Find the folder which represents the ElementType or FieldType you need to support, then install as a regular plugin. Just ensure you (obviously) have Feed Me installed.

### Why do I need an additional plugin to Feed Me?
Rather than have Feed Me handle the multitude of different ElementTypes and FieldTypes natively, they've been extracted out into their own plugins - nicknamed 'Helpers'. These are separately installed plugins.

This is firstly to make changes to these third-party integrations easier, in that a release for Feed Me doesn't need to be created for each new third-party integration. We can also squash bugs faster!

Secondly, it gives third-party developers the chance to collaborate on integrations far more easily as code is separated from the main Feed Me code.
