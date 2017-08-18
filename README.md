# Advanced authentication filters for SimpleSAMLphp

A SimpleSAMLphp module with a selection of authentication processing filters

## About

This module provides the following authentication processing filters:

### AttributeAddIdPInfo
This filter adds an attribute with information about the entity-id of the IdP where the user has been authenticated. This filter is appropriated for use on a brigded IdP configuration connected with a wayf service.

### CallRestApi
This filter enables REST calls to an API of choice and presents the response in a new attribute.
