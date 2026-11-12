# Editable package sources

`content.py` holds the curriculum, workflows, exercises, slide outline, notes and qualifications. `build_package.py` builds documents only inside this package. It does not change application code, accounts, database records or hosting.

Use a separate temporary Python virtual environment and install `requirements.txt` there. Do not add these libraries to application dependencies. Set `PYTHONDONTWRITEBYTECODE=1` when rebuilding to avoid incidental bytecode files.

Run the stages in order:

1. `python source/build_package.py curriculum`
2. Review the curriculum before proceeding.
3. Optionally run `node source/capture-public.mjs` for fresh read-only public screenshots. It uses the repository’s existing Playwright dependency and a machine-specific Chromium executable path; adjust that path to the approved local browser. It submits no forms and uses no credentials. Fresh captures are not automatically verified. Visually inspect each and only then mark the screenshot register entry `verified` with review evidence. Leave blocked captures pending.
4. `python source/build_package.py presentation`
5. `python source/build_package.py supporting`
6. `python source/review_layout.py`, then `node source/check-layout.mjs` if the local browser is available. This is a layout approximation, not native PowerPoint rendering.
7. Review PDF text/pages, complete the visual-review record, and run `python source/build_package.py qa` last to refresh the delivery report and hashes.

The commands above assume the current directory is the package root. In the delivered repository you can instead prefix each source path with `docs/training-package-2026-11-12/`.

Never insert real credentials, sessions, reset links, citizen cases or signed attendance into shared source files. Actual attendance and individual results belong in the authorized Council’s controlled copy, not a public repository. Do not mark screenshots or practical tasks verified without evidence. DOCX and PDF share structured content but have independent pagination. Open the files in the target Office software before delivery.
