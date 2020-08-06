<?php

namespace App\Controller\Administration;

use App\Entity\Character;
use App\Entity\MediaObject;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\LanguageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\LocaleField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserCrudController extends AbstractCrudController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('entity.user.header.singular')
            ->setEntityLabelInPlural("entity.user.header.plural")
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->setLabel('entity.user.label.id')->hideOnForm()->hideOnIndex();
        yield TextField::new('displayName')->setLabel('entity.user.label.displayName');
        yield EmailField::new('email')->setLabel('entity.user.label.email');
        yield ChoiceField::new('locale')->setLabel('entity.user.label.locale')->setChoices(['de-DE' => 'de-DE', 'en-US' => 'en-US', 'nl-BE' => 'nl-BE', 'fr-FR' => 'fr-FR', 'es-ES' => 'es-ES']);
        yield BooleanField::new('isActive')->setLabel('entity.user.label.isActive');
        yield BooleanField::new('confirmedEmail')->setLabel('entity.user.label.confirmedEmail');
        yield TextField::new('plainPassword')->setLabel('entity.user.label.newPassword')->hideOnDetail()->hideOnIndex();
        yield ChoiceField::new('roles')->setLabel('entity.user.label.roles')->setChoices(['User' => 'ROLE_USER', 'Administrator' => 'ROLE_ADMIN'])->allowMultipleChoices()->hideOnIndex();
        yield DateTimeField::new('registrationDate')->setLabel('entity.user.label.registrationDate')->hideOnIndex();
        yield AssociationField::new('characters')->setLabel('entity.user.label.characters')->autocomplete();
        yield AssociationField::new('mediaObjects')->setLabel('entity.user.label.mediaObjects')->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        $showCharactersIndex = Action::new('showCharactersIndex', false, 'fa fa-mask')
            ->linkToUrl(function (User $user) {
                $crudUrlGenerator = $this->get(CrudUrlGenerator::class);
                return $crudUrlGenerator->build()->setController(CharacterCrudController::class)->setAction(Crud::PAGE_INDEX)->set('query',$user->getId());
            })
            ->setHtmlAttributes(['title' => $this->translator->trans('entity.user.label.showCharacters', [], 'administration', $this->getUser()->getLocale())])
            ;

        $showCharactersDetail = Action::new('showCharactersDetail', 'entity.user.label.showCharacters', 'fa fa-mask')
            ->linkToUrl(function (User $user) {
                $crudUrlGenerator = $this->get(CrudUrlGenerator::class);
                return $crudUrlGenerator->build()->setController(CharacterCrudController::class)->setAction(Crud::PAGE_INDEX)->set('query',$user->getId());
            })
            ->setHtmlAttributes(['title' => $this->translator->trans('entity.user.label.showCharacters', [], 'administration', $this->getUser()->getLocale())])
        ;

        $showMediaIndex = Action::new('showMediaIndex', false, 'fa fa-images')
            ->linkToUrl(function (User $user) {
                $crudUrlGenerator = $this->get(CrudUrlGenerator::class);
                return $crudUrlGenerator->build()->setController(CharacterCrudController::class)->setAction(Crud::PAGE_INDEX)->set('query',$user->getId());
            })
            ->setHtmlAttributes(['title' => $this->translator->trans('entity.user.label.showCharacters', [], 'administration', $this->getUser()->getLocale())])
        ;

        $showMediaDetail = Action::new('showMediaDetail', 'entity.user.label.showMedia', 'fa fa-images')
            ->linkToUrl(function (User $user) {
                $crudUrlGenerator = $this->get(CrudUrlGenerator::class);
                return $crudUrlGenerator->build()->setController(CharacterCrudController::class)->setAction(Crud::PAGE_INDEX)->set('query',$user->getId());
            })
            ->setHtmlAttributes(['title' => $this->translator->trans('entity.user.label.showCharacters', [], 'administration', $this->getUser()->getLocale())])
        ;


        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $showMediaIndex)
            ->add(Crud::PAGE_DETAIL, $showMediaDetail)
            ->add(Crud::PAGE_INDEX, $showCharactersIndex)
            ->add(Crud::PAGE_DETAIL, $showCharactersDetail)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye')->setLabel(false)->setHtmlAttributes(['title' => $this->translator->trans('action.detail', [], 'EasyAdminBundle', $this->getUser()->getLocale())]);
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit')->setLabel(false)->setHtmlAttributes(['title' => $this->translator->trans('action.edit', [], 'EasyAdminBundle', $this->getUser()->getLocale())]);
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-user-times')->setLabel(false)->setHtmlAttributes(['title' => $this->translator->trans('action.delete', [], 'EasyAdminBundle', $this->getUser()->getLocale())]);
            });
    }

}
