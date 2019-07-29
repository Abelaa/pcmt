output "vpc-subnet-id" {
  value       = module.vpc.public_subnets[0]
  description = "Id of the first subnet of the VPC"
}

output "security-group-id" {
  value       = aws_security_group.pcmt-web.id
  description = "Id of the security group"
}

output "route53-zone-id" {
  value       = aws_route53_zone.pcmt.id
  description = "Id of the dns zone"
}