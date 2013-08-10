<?php

class SubmissionModel extends alxModel
{
  function __construct()
  {
    $this->idKey = 'submissionId';
    $this->table = 'submissions';
  }
}
