# Test-Driven Feature Enforcement

## Objective
Ensure that every new feature is fully validated with automated tests, and that the entire test suite passes before considering the task complete.

## Rules

1. **Mandatory Test Creation**
   - For every new feature, create appropriate automated tests.
   - Cover:
     - Happy paths
     - Edge cases
     - Error handling scenarios
   - Tests must reflect real usage, not trivial mocks.

2. **Test Coverage Alignment**
   - Ensure new code is meaningfully covered.
   - Avoid superficial or redundant tests.
   - Prioritize behavior validation over implementation details.

3. **Run Full Test Suite**
   - After implementing the feature, run all existing tests (not only new ones).

4. **Failure Handling**
   - If any test fails:
     - Identify root cause (do NOT patch blindly).
     - Fix the issue properly.
     - Re-run tests.
   - Repeat until ALL tests pass.

5. **Regression Protection**
   - If the new feature breaks existing behavior:
     - Either:
       - Fix the feature implementation, OR
       - Update tests ONLY if they were incorrect
   - Never bypass failing tests.

6. **Definition of Done**
   A feature is ONLY complete if:
   - All new tests are written
   - All tests (new + existing) pass
   - No regressions are introduced

7. **Optional Enhancements**
   - Suggest improvements in test structure if needed
   - Refactor fragile tests when detected

## Execution Style
- Think like a senior QA + developer combined
- Be strict: no passing = no done
- Avoid shortcuts or temporary fixes