<?php

namespace Zend\Db\Adapter;

interface DriverInterface
{
    const NAME_FORMAT_CAMELCASE = 'camelCase';
    const NAME_FORMAT_NATURAL = 'natural';

    /**
     * @param string $nameFormat
     * @return string
     */
    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE);

    /**
     * @return bool
     */
    public function checkEnvironment();

    /**
     * @return DriverConnectionInterface
     */
    public function getConnection();

    /**
     * @return DriverStatementInterface
     */
    public function createStatement($sqlOrResource);

    /**
     * @return DriverResultInterface
     */
    public function createResult($resource);

    /**
     * @return array
     */
    public function getPrepareTypeSupport();

    /**
     * @param $name
     * @return string
     */
    public function formatParameterName($name);

}
