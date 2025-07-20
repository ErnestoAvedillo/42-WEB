<?php
// MongoDB PHP Driver stubs for IntelliSense

namespace MongoDB\Driver {
    class Manager
    {
        public function __construct($uri, $uriOptions = [], $driverOptions = []) {}
        public function executeBulkWrite($namespace, \MongoDB\Driver\BulkWrite $bulk, $writeConcern = null) {}
        public function executeQuery($namespace, \MongoDB\Driver\Query $query, $readPreference = null) {}
    }

    class BulkWrite
    {
        public function insert($document) {}
        public function update($filter, $newObj, $updateOptions = []) {}
        public function delete($filter, $deleteOptions = []) {}
    }

    class Query
    {
        public function __construct($filter, $queryOptions = []) {}
    }

    class Cursor
    {
        public function toArray() {}
    }

    class WriteResult
    {
        public function getInsertedCount() {}
        public function getInsertedIds() {}
    }
}

namespace MongoDB\BSON {
    class UTCDateTime
    {
        public function __construct($milliseconds = null) {}
    }

    class ObjectId
    {
        public function __construct($id = null) {}
        public function __toString() {}
    }
}
