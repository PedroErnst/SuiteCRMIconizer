<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2018 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

namespace Iconizer\Verification;

use Iconizer\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;


/**
 * Class FileChecker
 * @package Iconizer\Verification
 */
class FileChecker
{
    /**
     * @var bool
     */
    private $error = false;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var
     */
    private $fileName;

    /**
     * @var
     */
    private $force;

    /**
     * @var
     */
    private $fullPath;

    /**
     * @var
     */
    private $targetDirectory;

    /**
     * FileChecker constructor.
     */
    public function __construct()
    {
        $this->fileSystem = new Filesystem();
    }

    /**
     * @param string $fileName
     * @param bool $force
     * @throws \Exception
     */
    public function check($fileName, $force)
    {
        $this->fileName =$fileName;
        $this->force = $force;
        $this->fullPath = Config::getVar('base_dir') . '/images/png/' . $this->fileName;
        $this->targetDirectory = Config::getVar('base_dir') . '/images/library';

        $this->runChecks();
    }

    /**
     * @throws \Exception
     */
    private function runChecks()
    {
        $this->checkFileName();
        $this->checkFileExists();
        $this->checkLibraryFolderIsWritable();
        $this->checkTargetFolderDoesNotExist();
    }

    /**
     * @throws \Exception
     */
    private function checkFileExists()
    {

        if (!$this->fileSystem->exists($this->fullPath)) {
            throw new \Exception("File doesn't exist: " . $this->fullPath);
        }
    }

    /**
     * @throws \Exception
     */
    private function checkLibraryFolderIsWritable()
    {
        if (!is_writable($this->targetDirectory)) {
            throw new \Exception("Library folder is not writable: " . $this->targetDirectory);
        }
    }

    /**
     * @throws \Exception
     */
    private function checkTargetFolderDoesNotExist()
    {
        if ($this->force) {
            return;
        }

        $targetFolder = $this->targetDirectory . '/' . pathinfo($this->fileName, PATHINFO_FILENAME);
        if ($this->fileSystem->exists($targetFolder)) {
            throw new \Exception("An icon with the same name already exists. Use -f to overwrite.");
        }
    }

    /**
     * @throws \Exception
     */
    private function checkFileName()
    {
        if (substr($this->fileName, -4) !== '.png') {
            throw new \Exception('File type must be .png!');
        }
    }

    /**
     * @return bool
     */
    public function lastError()
    {
        return $this->error;
    }
}