help:
	# test
	# test-watch
	# test-coverage

test:
	phpunit

test-watch:
	watch -n 1 --color phpunit --colors=always

test-coverage:
	phpunit --coverage-html=phpunit-coverage tests/
