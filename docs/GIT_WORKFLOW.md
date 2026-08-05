# Git Workflow

## Branch Strategy

```
main
  └── develop
        ├── feature/*
        ├── bugfix/*
        ├── hotfix/*
        └── release/*
```

## Branch Types

### Main (Production)
- `main` - Production-ready code
- Protected branch
- Requires PR review
- Direct pushes disabled

### Develop (Development)
- `develop` - Integration branch for features
- All features merged here first
- Code freeze before release

### Feature Branches
- `feature/module-name` - New feature development
- Branch from: `develop`
- Merge back to: `develop`
- Naming: `feature/auth`, `feature/student-module`

### Bugfix Branches
- `bugfix/issue-description` - Bug fixes
- Branch from: `develop`
- Merge back to: `develop`

### Hotfix Branches
- `hotfix/issue-description` - Production fixes
- Branch from: `main`
- Merge back to: `main` and `develop`
- Requires immediate attention

### Release Branches
- `release/v1.0.0` - Release preparation
- Branch from: `develop`
- Merge back to: `main`

## Workflow Steps

### Feature Development

```bash
# 1. Create feature branch
git checkout develop
git pull origin develop
git checkout -b feature/student-module

# 2. Develop and commit
git add .
git commit -m "feat(student): add student CRUD operations"

# 3. Keep branch updated
git fetch origin
git rebase origin/develop

# 4. Push and create PR
git push -u origin feature/student-module
# Create PR on GitHub

# 5. After review, merge PR
# Squash and merge on GitHub
```

### Hotfix Development

```bash
# 1. Create hotfix branch
git checkout main
git pull origin main
git checkout -b hotfix/critical-login-bug

# 2. Fix and commit
git add .
git commit -m "fix(auth): resolve login session timeout"

# 3. Push and merge
git push -u origin hotfix/critical-login-bug
# Create PR for main

# 4. After merge, update develop
git checkout develop
git merge main
git push origin develop
```

## Pull Request Rules

1. Title must follow commit convention
2. Description must explain what and why
3. Must pass all CI checks
4. Requires at least 1 approval
5. No unresolved conflicts
6. All comments must be resolved

## Git Commands Reference

```bash
# Branch operations
git branch                    # List branches
git checkout -b <branch>      # Create and switch
git branch -d <branch>       # Delete local
git push origin --delete <branch>  # Delete remote

# Stashing
git stash                     # Stash changes
git stash pop                 # Apply stash
git stash list                # List stashes

# Rebasing
git rebase -i HEAD~3         # Interactive rebase
git rebase develop           # Rebase on develop

# History
git log --oneline            # Short log
git log --graph              # Visual graph
git blame <file>             # File history

# Cleanup
git gc                       # Garbage collection
git prune                    # Prune unreachable
```

## Best Practices

1. Commit often with clear messages
2. Never commit directly to main/develop
3. Always pull before starting work
4. Rebase instead of merge when updating
5. Keep commits atomic (one change per commit)
6. Write meaningful commit messages
7. Delete branches after merging
