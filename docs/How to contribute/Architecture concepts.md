---
sidebar_label: Architecture concepts
sidebar_position: 1
---

# Logging

When an action* is taken, it gets logged with the following data :

- The model which has been eddited
- The volunteer that took the action
- The name of the involved attributes

:::note

An action is either a modification or a data creation

:::


:::info

The volunteer may be `null`. This happens only when a visitor have been edited or added and only when it was their own action.

This means a null value corresponds to a visitor auto editing themselves.

:::

