<?php

namespace VersionPress\ChangeInfos;

use Nette\Utils\Strings;
use VersionPress\Git\CommitMessage;
use VersionPress\Utils\StringUtils;

/**
 * Changes in Composer packages like installation, uninstallation, update.
 *
 * VP tags:
 *
 *     VP-Action: composer/(install|update|delete)/symfony/process
 *
 */
class ComposerChangeInfo extends TrackedChangeInfo
{

    private static $OBJECT_TYPE = "composer";

    /** @var string */
    private $packageName;

    /** @var string */
    private $action;

    /**
     * @param string $packageName Name of Composer package.
     * @param string $action See VP-Action tag documentation in the class docs
     */
    public function __construct($packageName, $action)
    {
        $this->packageName = $packageName;
        $this->action = $action;
    }

    public function getEntityName()
    {
        return self::$OBJECT_TYPE;
    }

    public function getAction()
    {
        return $this->action;
    }

    public static function buildFromCommitMessage(CommitMessage $commitMessage)
    {
        $actionTag = $commitMessage->getVersionPressTag(TrackedChangeInfo::ACTION_TAG);
        list(, $action, $packageName) = explode("/", $actionTag, 3);
        return new self($packageName, $action);
    }

    public function getChangeDescription()
    {
        return sprintf(
            "%s Composer package '%s'",
            Strings::capitalize(StringUtils::verbToPastTense($this->action)),
            $this->packageName
        );
    }

    protected function getActionTagValue()
    {
        return "{$this->getEntityName()}/{$this->getAction()}/" . $this->packageName;
    }

    public function getChangedFiles()
    {
        return [
            ["type" => "path", "path" => VP_PROJECT_ROOT . '/composer.json'],
            ["type" => "path", "path" => VP_PROJECT_ROOT . '/composer.lock'],
        ];
    }

    public function getCustomTags()
    {
        return [];
    }
}
