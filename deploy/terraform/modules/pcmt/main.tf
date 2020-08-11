terraform {
  required_version = ">= 0.12.0"

  required_providers {
    aws = {
      version = ">= 3.1"
    }
  }
}

provider "aws" {
  alias = "compute"
}

provider "aws" {
  alias = "network"
}
