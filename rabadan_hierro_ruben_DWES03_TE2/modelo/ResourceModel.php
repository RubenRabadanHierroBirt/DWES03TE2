<?php
require_once '../config/config.php';

class ResourceModel {
    private $file;

    public function __construct($filename) {
        $this->file = $filename;
    }

    public function getAll() {
        return readJsonFile($this->file);
    }

    public function getById($id) {
        $resources = readJsonFile($this->file);
        foreach ($resources as $resource) {
            if ($resource['id'] == $id) {
                return $resource;
            }
        }
        return null;
    }

    public function create($data) {
        $resources = readJsonFile($this->file);
        $data['id'] = count($resources) + 1;
        $resources[] = $data;
        writeJsonFile($this->file, $resources);
        return true;
    }

    public function update($id, $data) {
        $resources = readJsonFile($this->file);
        foreach ($resources as &$resource) {
            if ($resource['id'] == $id) {
                $resource = array_merge($resource, $data);
                writeJsonFile($this->file, $resources);
                return true;
            }
        }
        return false;
    }

    public function delete($id) {
        $resources = readJsonFile($this->file);
        $newResources = array_filter($resources, fn($resource) => $resource['id'] != $id);
        if (count($newResources) != count($resources)) {
            writeJsonFile($this->file, array_values($newResources));
            return true;
        }
        return false;
    }
}
