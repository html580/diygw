<?php
/**
 * @copyright 2012 Anthon Pang
 * @license MIT
 */

namespace VIPSoft\Unzip;

/**
 * Unzip class
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Unzip
{
    /**
     * @var array
     */
    static $statusStrings = array(
        \ZipArchive::ER_OK => 'No error',
        \ZipArchive::ER_MULTIDISK => 'Multi-disk zip archives not supported',
        \ZipArchive::ER_RENAME => 'Renaming temporary file failed',
        \ZipArchive::ER_CLOSE => 'Closing zip archive failed',
        \ZipArchive::ER_SEEK => 'Seek error',
        \ZipArchive::ER_READ => 'Read error',
        \ZipArchive::ER_WRITE => 'Write error',
        \ZipArchive::ER_CRC => 'CRC error',
        \ZipArchive::ER_ZIPCLOSED => 'Containing zip archive was closed',
        \ZipArchive::ER_NOENT => 'No such file',
        \ZipArchive::ER_EXISTS => 'File already exists',
        \ZipArchive::ER_OPEN => 'Can\'t open file',
        \ZipArchive::ER_TMPOPEN => 'Failure to create temporary file',
        \ZipArchive::ER_ZLIB => 'Zlib error',
        \ZipArchive::ER_MEMORY => 'Malloc failure',
        \ZipArchive::ER_CHANGED => 'Entry has been changed',
        \ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported',
        \ZipArchive::ER_EOF => 'Premature EOF',
        \ZipArchive::ER_INVAL => 'Invalid argument',
        \ZipArchive::ER_NOZIP => 'Not a zip archive',
        \ZipArchive::ER_INTERNAL => 'Internal error',
        \ZipArchive::ER_INCONS => 'Zip archive inconsistent',
        \ZipArchive::ER_REMOVE => 'Can\'t remove file',
        \ZipArchive::ER_DELETED => 'Entry has been deleted',
    );

    /**
     * Make sure target path ends in '/'
     *
     * @param string $path
     *
     * @return string
     */
    private function fixPath($path)
    {
        if (substr($path, -1) === '/') {
            $path .= '/';
        }

        return $path;
    }

    /**
     * Open .zip archive
     *
     * @param string $zipFile
     *
     * @return \ZipArchive
     */
    private function openZipFile($zipFile)
    {
        $zipArchive = new \ZipArchive;

        if ($zipArchive->open($zipFile) !== true) {
            throw new \Exception('Error opening '.$zipFile);
        }

        return $zipArchive;
    }

    /**
     * Extract list of filenames from .zip
     *
     * @param \ZipArchive $zipArchive
     *
     * @return array
     */
    private function extractFilenames(\ZipArchive $zipArchive)
    {
        $filenames = array();
        $fileCount = $zipArchive->numFiles;

        for ($i = 0; $i < $fileCount; $i++) {
            if (($filename = $this->extractFilename($zipArchive, $i)) !== false) {
                $filenames[] = $filename;
            }
        }

        return $filenames;
    }

    /**
     * Test for valid filename path
     *
     * The .zip file is untrusted input.  We check for absolute path (i.e., leading slash),
     * possible directory traversal attack (i.e., '..'), and use of PHP wrappers (i.e., ':').
     *
     * @param string $path
     *
     * @return boolean
     */
    private function isValidPath($path)
    {
        $pathParts = explode('/', $path);

        if (!strncmp($path, '/', 1) ||
            array_search('..', $pathParts) !== false ||
            strpos($path, ':') !== false)
        {
            return false;
        }

        return true;
    }

    /**
     * Extract filename from .zip
     *
     * @param \ZipArchive $zipArchive Zip file
     * @param integer     $fileIndex  File index
     *
     * @return string
     */
    private function extractFilename(\ZipArchive $zipArchive, $fileIndex)
    {
        $entry = $zipArchive->statIndex($fileIndex);

        // convert Windows directory separator to Unix style
        $filename  = str_replace('\\', '/', $entry['name']);

        if ($this->isValidPath($filename)) {
            return $filename;
        }

        throw new \Exception('Invalid filename path in zip archive');
    }

    /**
     * Get error
     *
     * @param integer $status ZipArchive status
     *
     * @return string
     */
    private function getError($status)
    {
        $statusString = isset($this->statusStrings[$status])
            ? $this->statusStrings[$status]
            :'Unknown status';

        return $statusString . '(' . $status . ')';
    }

    /**
     * Extract zip file to target path
     *
     * @param string $zipFile    Path of .zip file
     * @param string $targetPath Extract to this target (destination) path
     *
     * @return mixed Array of filenames corresponding to the extracted files
     *
     * @throw \Exception
     */
    public function extract($zipFile, $targetPath)
    {
        $zipArchive = $this->openZipFile($zipFile);
        $targetPath = $this->fixPath($targetPath);
        $filenames  = $this->extractFilenames($zipArchive);

        if ($zipArchive->extractTo($targetPath, $filenames) === false) {
            throw new \Exception($this->getError($zipArchive->status));
        }

        $zipArchive->close();

        return $filenames;
    }
}
