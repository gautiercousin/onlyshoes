#!/usr/bin/env python3
"""
Formats embedding JSON output for PostgreSQL vector type.
Usage: curl ... | python format_embedding.py
"""
import sys
import json

data = json.load(sys.stdin)
embedding = data['embedding']
vector_str = '[' + ','.join(str(x) for x in embedding) + ']'
print(f"'{vector_str}'::vector(384)")
