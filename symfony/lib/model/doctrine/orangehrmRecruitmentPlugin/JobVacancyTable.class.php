<?php

/**
 * JobVacancyTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class JobVacancyTable extends PluginJobVacancyTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object JobVacancyTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('JobVacancy');
    }
}