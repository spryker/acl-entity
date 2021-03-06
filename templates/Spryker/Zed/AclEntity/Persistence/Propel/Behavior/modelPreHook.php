// phpcs:ignoreFile
/**
 * @var string|null $action
 */
/** @var \Spryker\Zed\AclEntity\Business\AclEntityFacadeInterface $aclEntityFacade */
$aclEntityFacade = \Spryker\Zed\Kernel\Locator::getInstance()->aclEntity()->facade();
if ($aclEntityFacade->isActive()) {
    $aclEntityMetadataConfigTransfer = $aclEntityFacade->getAclEntityMetadataConfig();
    if (!in_array(get_class($this), $aclEntityMetadataConfigTransfer->getAclEntityAllowList())) {
        $this->getPersistenceFactory()
            ->createAclModelDirector($aclEntityMetadataConfigTransfer)
            ->inspect<?php echo $action ?? ''; ?>($this);
    }
}
