# Symfony connector for SPŠ Ostrov libsso library

This is just a symfony connector for the original [spsostrov/libsso](https://github.com/marek-sterzik/spsostrov-libsso) library.
It provides extra modules for the Symfony security bundle allowing to communicate with the school's SSO system.

## Installation

Install the package into the project:

```bash
composer require spsostrov/libsso-symfony
```

Then add the bundle `SPSOstrov\SSOBundle\SSOBundle` in your application bundles:

```php
return [
    # ... (other bundles)
    SPSOstrov\SSOBundle\SSOBundle::class => ['all' => true],
];
```

## Usage

The libsso symfony connector is a Symfony bundle providing extra modules to the standard symfony security bundle.
It provides two modules:

1. The `spsostrov_sso` user provider.
2. The `spsostrov_sso` authenticator.

Both modules are indended to be used together. The `spsostrov_sso` authenticator requires to use the corresponding
user provider and will not work with any other user providers. On the other hand, you may override the default
user provier for the given firewall rule by a specific user provider by specifying the `provider` argument.


## Configuration

This library may be configured via `security.yaml` config file. A typical `security.yaml` may look like this:

```yaml
security:
    providers:
        spsostrov_sso:
            spsostrov_sso:
                # Set the service id of the service responsible for the role decision.
                # (instance of interface SPSOstrov\SSOBundle\SSORoleDeciderInterface)
                # If no role decider is used, the default role decider is applied
                # providing the roles: ROLE_USER, ROLE_TEACHER, ROLE_STUDENT.
                # (optional)
                role_decider: null
    firewalls:
        main:
            lazy: true

            # You need to use the spsostrov_sso user provider:
            provider: spsostrov_sso

            # Configure the spsostrov_sso authenticator
            spsostrov_sso:
              # Setup the route name used for log in:
              login_path: login

              # Override the default user provider.
              # (optional)
              provider: null

              # Service id of the SPSOstrov\SSO\SSO service.
              # Useful for overriding the default SSO instance parameters.
              # (optional)
              sso: null

            # configure standard logout:
            logout:
              path: logout
```

## Users

The object representing the authenticated user is an instance of `SPSOstrov\SSOBundle\SSOUser` which is a subclass
of the original `SPSOstrov\SSO\SSOUser` class from the original libsso library. The user therefore provides all
functions available in the original libsso library and moreover provides the `getRoles()` method (not available
in libsso) returning the assigned roles by the role decider. (See role deciders)

## Role deciders

A role decider is a service allowing to assign custom roles to existing users. You may do the role decision proces
in any way you want depending on the data from the database and also on the data from the user. All you need is
to create a symfony service implementing the `SPSOstrov\SSOBundle\SSORoleDeciderInterface` with its single method
`decideRoles()`, which takes one argument the user being decided and is responsible for returning an array of
all roles assigned to the given user. If no role decider is set up, a default role decider is used. It will decide
the roles `ROLE_USER` (any user will have this role), `ROLE_TEACHER` (teachers) and `ROLE_STUDENT` (students).
