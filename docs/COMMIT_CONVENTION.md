# Git Commit Message Convention

## Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

## Types

| Type | Description |
|------|-------------|
| `feat` | A new feature |
| `fix` | A bug fix |
| `docs` | Documentation only changes |
| `style` | Changes that do not affect the meaning of the code (formatting, etc.) |
| `refactor` | A code change that neither fixes a bug nor adds a feature |
| `perf` | A code change that improves performance |
| `test` | Adding missing tests or correcting existing tests |
| `build` | Changes that affect the build system or external dependencies |
| `ci` | Changes to CI configuration files and scripts |
| `chore` | Other changes that don't modify src or test files |

## Scope

The scope could be anything specifying the place of the commit change.

Examples:
- `feat(auth):`
- `fix(dashboard):`
- `docs(readme):`
- `refactor(api):`
- `test(student):`

## Subject

The subject contains a succinct description of the change:

- Use the imperative, present tense: "add" not "added" nor "adds"
- Don't capitalize the first letter
- No dot (.) at the end
- Use lowercase

## Examples

```
feat(auth): add login functionality
fix(validation): fix email validation regex
docs(api): update API documentation
style(format): format code with prettier
refactor(service): improve student service layer
test(unit): add unit tests for payment service
```

## Body

Just as in the subject, use the imperative, present tense. The body should include the motivation for the change and contrast this with previous behavior.

## Footer

The footer should contain any information about Breaking Changes and is also the place to reference GitHub issues that this commit Closes.

```
Closes #123
See also #456
```

## Best Practices

1. Keep commits small and focused
2. Write meaningful commit messages
3. Don't commit incomplete work
4. Test before committing
5. Commit early, commit often
